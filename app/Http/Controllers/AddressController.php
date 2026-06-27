<?php

namespace App\Http\Controllers;

use App\Services\RajaOngkirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct(private readonly RajaOngkirService $rajaOngkir) {}

    public function index()
    {
        return view('pages.dashboard.index');
    }
}
