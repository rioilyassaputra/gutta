<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::withCount(['products' => fn($q) => $q->active()])->get();

        return view('pages.shop.index', compact('categories'));
    }

    public function show(Product $product): View
    {
        if (! $product->is_active) {
            abort(404);
        }

        $product->load(['images', 'variants', 'category']);

        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['images', 'variants'])
            ->take(4)
            ->get();

        return view('pages.shop.show', compact('product', 'related'));
    }

    public function byCategory(Category $category): View
    {
        $categories = Category::withCount(['products' => fn($q) => $q->active()])->get();

        return view('pages.shop.index', compact('categories', 'category'));
    }
}
