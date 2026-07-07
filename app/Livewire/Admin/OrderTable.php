<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
class OrderTable extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    #[Url]
    public string $courierFilter = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }

    public function render(
        \App\Services\PaymentServiceInterface $paymentService,
        \App\Services\OrderService $orderService
    ) {
        // Auto-check pending payment status from gateway for active orders
        $pendingOrders = Order::where('status', 'pending_payment')->take(10)->get();

        foreach ($pendingOrders as $pendingOrder) {
            if (method_exists($paymentService, 'getTransactionStatus')) {
                $status = $paymentService->getTransactionStatus($pendingOrder);
                if (in_array(strtolower($status ?? ''), ['completed', 'paid', 'settlement', 'success'])) {
                    $orderService->markAsPaid($pendingOrder);
                }
            }
        }

        $query = Order::with(['user', 'items'])
            ->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%"));
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->courierFilter) {
            $query->where('courier', $this->courierFilter);
        }

        $orders = $query->paginate(20);
        return view('livewire.admin.order-table', compact('orders'));
    }

    public function placeholder()
    {
        return view('components.skeleton-loader');
    }
}
