<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class ProductService
{
    /**
     * Create a new product with images.
     */
    public function createProduct($sellerProfile, array $data, array $images = [])
    {
        return DB::transaction(function () use ($sellerProfile, $data, $images) {
            // 1. Create Product
            $product = Product::create([
                'seller_id' => $sellerProfile->user_id,
                'category_id' => $data['category_id'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'stock_quantity' => $data['stock_quantity'],
                'type' => $data['type'] ?? 'physical_good',
            ]);

            // 2. Handle Images
            if (!empty($images)) {
                $this->uploadImages($product, $images);
            }

            return $product;
        });
    }

    /**
     * Update existing product.
     */
    public function updateProduct(Product $product, array $data, array $newImages = [])
    {
        return DB::transaction(function () use ($product, $data, $newImages) {
            $product->update([
                'category_id' => $data['category_id'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'stock_quantity' => $data['stock_quantity'],
                'type' => $data['type'] ?? 'physical_good',
            ]);

            if (!empty($newImages)) {
                $this->uploadImages($product, $newImages);
            }

            return $product;
        });
    }

    /**
     * Upload and attach images to product.
     */
    protected function uploadImages(Product $product, array $files)
    {
        foreach ($files as $index => $file) {
            if ($file instanceof UploadedFile) {
                $path = $file->store('products', 'public');
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => ($product->images()->count() === 0 && $index === 0), // First image is primary if no images exist
                    'display_order' => $product->images()->count() + $index,
                ]);
            }
        }
    }

    /**
     * Delete a product and its images.
     */
    public function deleteProduct(Product $product)
    {
        // Delete images from storage
        foreach ($product->images as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
        }
        
        $product->delete();
    }
}
