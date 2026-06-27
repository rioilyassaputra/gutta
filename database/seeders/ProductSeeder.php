<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'T-Shirt', 'slug' => 't-shirt'],
            ['name' => 'Hoodie', 'slug' => 'hoodie'],
            ['name' => 'Pants', 'slug' => 'pants'],
            ['name' => 'Accessories', 'slug' => 'accessories'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        $tshirtCategory = Category::where('slug', 't-shirt')->first();
        $hoodieCategory = Category::where('slug', 'hoodie')->first();
        $pantsCategory = Category::where('slug', 'pants')->first();

        $products = [
            [
                'name'         => 'Gutta Essential Tee Black',
                'slug'         => 'gutta-essential-tee-black',
                'description'  => 'T-shirt essential Gutta dengan bahan premium cotton combed 30s. Fit boxy, feel clean.',
                'price'        => 185000,
                'weight_grams' => 200,
                'category_id'  => $tshirtCategory->id,
                'is_active'    => true,
                'sizes'        => ['S', 'M', 'L', 'XL'],
                'stock_per_size' => [12, 20, 18, 10],
            ],
            [
                'name'         => 'Gutta Brutalisme Tee',
                'slug'         => 'gutta-brutalisme-tee',
                'description'  => 'Graphic tee dengan desain brutalisme khas Gutta. Screen print premium.',
                'price'        => 225000,
                'weight_grams' => 210,
                'category_id'  => $tshirtCategory->id,
                'is_active'    => true,
                'sizes'        => ['S', 'M', 'L', 'XL', 'XXL'],
                'stock_per_size' => [5, 15, 15, 8, 3],
            ],
            [
                'name'         => 'Gutta Phantom Hoodie',
                'slug'         => 'gutta-phantom-hoodie',
                'description'  => 'Hoodie heavyweight 380gsm. Unisex, oversized fit. Warna charcoal black.',
                'price'        => 485000,
                'weight_grams' => 600,
                'category_id'  => $hoodieCategory->id,
                'is_active'    => true,
                'sizes'        => ['M', 'L', 'XL'],
                'stock_per_size' => [8, 12, 6],
            ],
            [
                'name'         => 'Gutta Cargo Pants',
                'slug'         => 'gutta-cargo-pants',
                'description'  => 'Cargo pants dengan potongan utility modern. Material ripstop anti-crease.',
                'price'        => 395000,
                'weight_grams' => 450,
                'category_id'  => $pantsCategory->id,
                'is_active'    => true,
                'sizes'        => ['S', 'M', 'L'],
                'stock_per_size' => [4, 10, 7],
            ],
        ];

        foreach ($products as $productData) {
            $sizes        = $productData['sizes'];
            $stockPerSize = $productData['stock_per_size'];
            unset($productData['sizes'], $productData['stock_per_size']);

            $product = Product::firstOrCreate(['slug' => $productData['slug']], $productData);

            // Create variants
            foreach ($sizes as $i => $size) {
                ProductVariant::firstOrCreate(
                    ['product_id' => $product->id, 'size' => $size],
                    [
                        'sku'              => strtoupper(Str::slug($product->name . '-' . $size)),
                        'stock'            => $stockPerSize[$i],
                        'additional_price' => 0,
                    ]
                );
            }
        }
    }
}
