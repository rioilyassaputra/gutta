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

    public function render()
    {
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
