<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ImageService
{
    /**
     * Store a product image, converting it to WebP format.
     * Max width 1200px, quality 80.
     *
     * @return string Storage path (relative)
     */
    public function storeProductImage(UploadedFile $file, int $productId): string
    {
        $filename = "products/{$productId}/" . uniqid('img_', true) . '.webp';

        $image = Image::read($file)
            ->scaleDown(width: 1200)
            ->toWebp(quality: 80);

        Storage::disk('public')->put($filename, (string) $image);

        return $filename;
    }

    /**
     * Delete a product image from storage.
     */
    public function deleteProductImage(string $path): void
    {
        Storage::disk('public')->delete($path);
    }
}
