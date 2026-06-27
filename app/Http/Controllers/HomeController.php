<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProducts = Product::active()
            ->with(['images', 'variants', 'category'])
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::withCount(['products' => fn($q) => $q->active()])
            ->having('products_count', '>', 0)
            ->get();

        return view('pages.home', compact('featuredProducts', 'categories'));
    }
}
