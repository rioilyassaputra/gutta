<?php

return [
    'api_key'        => env('RAJAONGKIR_API_KEY', ''),
    'origin_city_id' => env('RAJAONGKIR_ORIGIN_CITY_ID', 540), // 540 = Boyolali
    'couriers'       => env('RAJAONGKIR_COURIERS', 'jne,jnt,sicepat,pos'),
    'base_url'       => 'https://rajaongkir.komerce.id/api/v1',
];
