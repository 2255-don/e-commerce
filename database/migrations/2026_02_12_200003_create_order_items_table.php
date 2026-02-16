<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignUuid('order_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('product_id')->constrained()->onDelete('cascade');
            // Seller is a User (through SellerProfile -> User), but we store the User ID of the seller here for quick access
            $table->foreignUuid('seller_id')->constrained('users')->onDelete('cascade');
            
            $table->integer('quantity');
            $table->decimal('price_at_purchase', 10, 2);
            $table->decimal('subtotal', 10, 2);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
