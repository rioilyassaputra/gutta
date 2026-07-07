<?php

namespace App\Livewire\Dashboard;

use App\Models\Order;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
class OrderList extends Component
{
    use WithPagination;

    #[Url(as: 'status')]
    public string $filterStatus = '';

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function mount(
        \App\Services\PaymentServiceInterface $paymentService,
        \App\Services\OrderService $orderService
    ): void {
        // Auto-check pending payment status ONCE on component mount
        $pendingOrders = Order::where('user_id', auth()->id())
            ->where('status', 'pending_payment')
            ->get();

        foreach ($pendingOrders as $pendingOrder) {
            if (method_exists($paymentService, 'getTransactionStatus')) {
                $status = $paymentService->getTransactionStatus($pendingOrder);
                if (in_array(strtolower($status ?? ''), ['completed', 'paid', 'settlement', 'success'])) {
                    $orderService->markAsPaid($pendingOrder);
                }
            }
        }
    }

    public function render()
    {
        $query = Order::where('user_id', auth()->id())
            ->with(['items.variant.product.images'])
            ->latest();

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $orders = $query->paginate(10);

        return view('livewire.dashboard.order-list', compact('orders'));
    }

    public function placeholder()
    {
        return view('components.skeleton-loader');
    }
}
