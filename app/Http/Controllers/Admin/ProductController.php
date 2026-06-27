<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private readonly ImageService $imageService) {}

    public function index(): View
    {
        $products = Product::withTrashed()
            ->with(['category', 'images', 'variants'])
            ->latest()
            ->paginate(20);

        return view('pages.admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::all();
        return view('pages.admin.products.create', compact('categories'));
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = Product::create($request->validated());

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $this->imageService->storeProductImage($image, $product->id);
                ProductImage::create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    'order'      => $index,
                ]);
            }
        }

        // Handle variants
        if ($request->has('variants')) {
            foreach ($request->input('variants', []) as $variant) {
                ProductVariant::create(array_merge($variant, ['product_id' => $product->id]));
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        $product->load(['images', 'variants', 'category']);
        $categories = Category::all();
        return view('pages.admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $currentCount = $product->images()->count();
            foreach ($request->file('images') as $index => $image) {
                $path = $this->imageService->storeProductImage($image, $product->id);
                ProductImage::create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    'order'      => $currentCount + $index,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete(); // soft delete
        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
