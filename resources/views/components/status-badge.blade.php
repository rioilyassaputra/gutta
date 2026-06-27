@props(['status'])

@php
$class = match($status) {
    'pending_payment' => 'badge-pending',
    'paid'            => 'badge-paid',
    'processing'      => 'badge-processing',
    'shipped'         => 'badge-shipped',
    'completed'       => 'badge-completed',
    'cancelled'       => 'badge-cancelled',
    default           => 'bg-gray-500 text-white',
};
$label = match($status) {
    'pending_payment' => 'Menunggu Bayar',
    'paid'            => 'Dibayar',
    'processing'      => 'Diproses',
    'shipped'         => 'Dikirim',
    'completed'       => 'Selesai',
    'cancelled'       => 'Dibatalkan',
    default           => ucfirst($status),
};
@endphp

<span class="{{ $class }}">{{ $label }}</span>
