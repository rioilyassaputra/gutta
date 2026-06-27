<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\ProductVariant;
use Livewire\Attributes\On;
use Livewire\Component;

class Dashboard extends Component
{
    public array $stats = [];
    public $recentOrders = [];

    #[On('stock-updated')]
    public function refreshStats(): void
    {
        $this->loadData();
    }

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->stats = [
            'orders_today'     => Order::whereDate('created_at', today())->count(),
            'revenue_today'    => Order::whereDate('created_at', today())
                ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
                ->sum('total'),
            'need_processing'  => Order::where('status', 'paid')->count(),
            'low_stock'        => ProductVariant::where('stock', '>', 0)
                ->where('stock', '<=', 5)
                ->with('product')
                ->get(),
        ];

        $this->recentOrders = Order::with(['user', 'items'])
            ->latest()
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
