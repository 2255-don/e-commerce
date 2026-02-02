<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false; // Usually pivot tables or simple item tables might not need timestamps, but checking SQL... 
    // SQL shows created_at for order_items but no updated_at. Simple Model is fine, let's include timestamps handling if needed. 
    // SQL: `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP. No updated_at.
    // So we should set $timestamps = false; and maybe manually set created_at or let DB handle it.
    // Actually, Eloquent expects both by default. Let's set public $timestamps = false; and add protected $dates = ['created_at'];

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
