<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan Dikirim! — Gutta Store</title>
</head>
<body style="background-color: #000000; color: #ffffff; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 40px; -webkit-text-size-adjust: none;">
    <div style="max-width: 600px; margin: 0 auto; border: 2px solid #6d28d9; padding: 30px; background-color: #0d0d0d;">
        <!-- Header -->
        <div style="border-bottom: 2px solid #1f1f1f; padding-bottom: 20px; text-align: center;">
            <h1 style="color: #ffffff; font-size: 24px; font-weight: 900; letter-spacing: 2px; margin: 0; text-transform: uppercase;">GUTTA STORE</h1>
            <span style="color: #6d28d9; font-size: 10px; font-weight: bold; letter-spacing: 4px; text-transform: uppercase; display: block; margin-top: 5px;">Order Shipped</span>
        </div>

        <!-- Body -->
        <div style="padding: 30px 0; line-height: 1.6;">
            <p style="margin: 0 0 20px 0; font-size: 14px;">Halo <strong>{{ $order->user->name }}</strong>,</p>
            <p style="margin: 0 0 30px 0; font-size: 14px; color: #a1a1aa;">Pesanan Anda dengan nomor referensi <strong>{{ $order->order_number }}</strong> telah diserahkan ke pihak ekspedisi untuk dikirimkan ke alamat tujuan Anda.</p>

            <!-- Shipment Info Card -->
            <div style="background-color: #000000; border: 1px solid #1f1f1f; padding: 25px; margin-bottom: 30px;">
                <p style="margin: 0 0 15px 0; font-size: 12px; color: #a1a1aa; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">Informasi Pengiriman</p>
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <tr>
                        <td style="padding: 5px 0; color: #71717a; width: 120px;">Kurir:</td>
                        <td style="padding: 5px 0; color: #ffffff; font-weight: bold; text-transform: uppercase;">{{ $order->courier }} ({{ $order->courier_service }})</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #71717a;">Nomor Resi / AWB:</td>
                        <td style="padding: 5px 0; color: #ffffff; font-family: monospace; font-size: 15px; font-weight: bold;">{{ $order->tracking_number }}</td>
                    </tr>
                </table>
            </div>

            <!-- Call to Action -->
            <div style="text-align: center; margin-top: 40px; margin-bottom: 20px;">
                @if($order->tracking_url)
                    <a href="{{ $order->tracking_url }}" target="_blank" style="background-color: #6d28d9; color: #ffffff; text-decoration: none; font-weight: bold; text-transform: uppercase; font-size: 12px; letter-spacing: 2px; padding: 15px 30px; display: inline-block; border: 2px solid #6d28d9; margin-right: 10px;">
                        Lacak Pengiriman
                    </a>
                @endif
                <a href="{{ route('orders.show', $order->order_number) }}" style="background-color: transparent; color: #a1a1aa; text-decoration: none; font-weight: bold; text-transform: uppercase; font-size: 12px; letter-spacing: 2px; padding: 15px 30px; display: inline-block; border: 2px solid #1f1f1f;">
                    Detail Pesanan
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
