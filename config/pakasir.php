<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pakasir Payment Gateway Configuration
    |--------------------------------------------------------------------------
    */
    'slug' => env('PAKASIR_SLUG', 'gutta'),
    'api_key' => env('PAKASIR_API_KEY', ''),
    'base_url' => env('PAKASIR_BASE_URL', 'https://app.pakasir.com/api'),
];
