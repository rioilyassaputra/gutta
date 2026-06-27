<?php

return [
    'server_key'    => env('MIDTRANS_SERVER_KEY', ''),
    'client_key'    => env('MIDTRANS_CLIENT_KEY', ''),
    'merchant_id'   => env('MIDTRANS_MERCHANT_ID', ''),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'snap_url'      => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js',
];
