<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pesanan — Gutta Store</title>
</head>
<body style="background-color: #000000; color: #ffffff; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 40px; -webkit-text-size-adjust: none;">
    <div style="max-width: 600px; margin: 0 auto; border: 2px solid #6d28d9; padding: 30px; background-color: #0d0d0d;">
        <!-- Header -->
        <div style="border-bottom: 2px solid #1f1f1f; padding-bottom: 20px; text-align: center;">
            <h1 style="color: #ffffff; font-size: 24px; font-weight: 900; letter-spacing: 2px; margin: 0; text-transform: uppercase;">GUTTA STORE</h1>
            <span style="color: #6d28d9; font-size: 10px; font-weight: bold; letter-spacing: 4px; text-transform: uppercase; display: block; margin-top: 5px;">Order Confirmed</span>
        </div>

        <!-- Body -->
        <div style="padding: 30px 0; line-height: 1.6;">
            <p style="margin: 0 0 20px 0; font-size: 14px;">Halo <strong>{{ $order->user->name }}</strong>,</p>
            <p style="margin: 0 0 30px 0; font-size: 14px; color: #a1a1aa;">Terima kasih atas pesanan Anda. Kami telah menerima pembayaran Anda dan saat ini sedang mempersiapkan pesanan Anda.</p>

            <!-- Order Info -->
            <div style="background-color: #000000; border: 1px solid #1f1f1f; padding: 20px; margin-bottom: 30px;">
                <p style="margin: 0 0 10px 0; font-size: 12px; color: #a1a1aa; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">Detail Pesanan</p>
                <p style="margin: 0; font-family: monospace; font-size: 16px; font-weight: bold; color: #ffffff;">{{ $order->order_number }}</p>
                <p style="margin: 5px 0 0 0; font-size: 12px; color: #71717a;">Tanggal: {{ $order->created_at->format('d F Y, H:i') }} WIB</p>
            </div>

            <!-- Items Table -->
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                <thead>
                    <tr style="border-bottom: 2px solid #1f1f1f; text-align: left;">
                        <th style="padding: 10px 0; font-size: 11px; text-transform: uppercase; color: #71717a; font-weight: bold;">Item</th>
                        <th style="padding: 10px 0; font-size: 11px; text-transform: uppercase; color: #71717a; font-weight: bold; text-align: center; width: 60px;">Qty</th>
                        <th style="padding: 10px 0; font-size: 11px; text-transform: uppercase; color: #71717a; font-weight: bold; text-align: right; width: 120px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr style="border-bottom: 1px solid #1f1f1f;">
                            <td style="padding: 15px 0; font-size: 14px;">
                                <strong style="color: #ffffff;">{{ $item->product_name }}</strong>
                                <span style="font-size: 11px; color: #71717a; display: block; margin-top: 2px; text-transform: uppercase;">Size: {{ $item->variant_detail }}</span>
                            </td>
                            <td style="padding: 15px 0; font-size: 14px; text-align: center; color: #ffffff;">{{ $item->qty }}</td>
                            <td style="padding: 15px 0; font-size: 14px; text-align: right; font-weight: bold; color: #ffffff;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <!-- Calculations -->
                    <tr>
                        <td colspan="2" style="padding: 20px 0 5px 0; font-size: 13px; color: #a1a1aa; text-align: right;">Subtotal Produk:</td>
                        <td style="padding: 20px 0 5px 0; font-size: 13px; color: #ffffff; text-align: right; font-weight: bold;">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 5px 0; font-size: 13px; color: #a1a1aa; text-align: right;">Ongkos Kirim:</td>
                        <td style="padding: 5px 0; font-size: 13px; color: #ffffff; text-align: right; font-weight: bold;">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="border-top: 1px dashed #1f1f1f;">
                        <td colspan="2" style="padding: 15px 0 0 0; font-size: 15px; color: #ffffff; font-weight: bold; text-align: right; text-transform: uppercase; letter-spacing: 1px;">Total Bayar:</td>
                        <td style="padding: 15px 0 0 0; font-size: 16px; color: #6d28d9; text-align: right; font-weight: 900;">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Call to Action -->
            <div style="text-align: center; margin-top: 40px;">
                <a href="{{ route('orders.show', $order->order_number) }}" style="background-color: #6d28d9; color: #ffffff; text-decoration: none; font-weight: bold; text-transform: uppercase; font-size: 12px; letter-spacing: 2px; padding: 15px 30px; display: inline-block; border: 2px solid #6d28d9; transition: background-color 0.15s ease;">
                    Lihat Detail Pesanan
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div style="border-top: 2px solid #1f1f1f; padding-top: 20px; text-align: center; font-size: 11px; color: #71717a;">
            <p style="margin: 0 0 5px 0;">&copy; {{ date('Y') }} Gutta Store. All rights reserved.</p>
            <p style="margin: 0;">Boyolali, Jawa Tengah &mdash; Indonesia</p>
        </div>
    </div>
</body>
</html>
