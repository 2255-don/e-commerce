<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'seller_id',
        'category_id',
        'title',
        'description',
        'price',
        'stock_quantity',
        'type',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];

    public function seller()
    {
        return $this->belongsTo(SellerProfile::class, 'seller_id');
    }

    public function sellerUser()
    {
        return $this->hasOneThrough(User::class, SellerProfile::class, 'id', 'id', 'seller_id', 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('display_order');
    }

    public function getThumbnailUrlAttribute()
    {
        $primary = $this->images()->where('is_primary', true)->first();
        return $primary ? asset('storage/' . $primary->image_path) : asset('assets/img/products/default-product.png');
    }
}
