<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateTrackingRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function index(): View
    {
        return view('pages.admin.orders.index');
    }

    public function show(Order $order): View
    {
        $order->load(['items.variant.product.images', 'user']);
        return view('pages.admin.orders.show', compact('order'));
    }

    public function update(Order $order): RedirectResponse
    {
        // Toggle status: paid → processing
        if ($order->status === 'paid') {
            $order->update(['status' => 'processing']);
        }

        return redirect()->back()->with('success', 'Status pesanan diperbarui.');
    }

    public function updateTracking(UpdateTrackingRequest $request, Order $order): RedirectResponse
    {
        $this->orderService->updateTracking($order, $request->validated('tracking_number'));

        return redirect()->back()->with('success', 'Nomor resi berhasil disimpan dan notifikasi terkirim.');
    }
}
