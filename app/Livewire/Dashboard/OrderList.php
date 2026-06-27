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
