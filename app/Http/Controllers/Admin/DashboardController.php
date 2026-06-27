<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
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

        $recentOrders = Order::with(['user', 'items'])
            ->latest()
            ->take(10)
            ->get();

        return view('pages.admin.dashboard', compact('stats', 'recentOrders'));
    }
}
