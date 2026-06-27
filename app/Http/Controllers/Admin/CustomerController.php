<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = User::where('role', 'buyer')
            ->withCount('orders')
            ->withSum(['orders' => fn($q) => $q->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])], 'total')
            ->latest()
            ->paginate(25);

        return view('pages.admin.customers.index', compact('customers'));
    }

    public function export(): Response
    {
        $customers = User::where('role', 'buyer')
            ->withCount('orders')
            ->withSum(['orders' => fn($q) => $q->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])], 'total')
            ->get();

        $csv = "Nama,Email,WhatsApp,Total Pesanan,Total Spend,Tanggal Daftar\n";
        foreach ($customers as $c) {
            $csv .= implode(',', [
                $c->name,
                $c->email,
                $c->whatsapp ?? '-',
                $c->orders_count,
                $c->orders_sum_total ?? 0,
                $c->created_at->format('d/m/Y'),
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="pelanggan-' . now()->format('Ymd') . '.csv"',
        ]);
    }
}
