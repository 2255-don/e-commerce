<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add the missing price_at_addition column
        // Add the missing price_at_addition column if it doesn't exist
        if (!Schema::hasColumn('cart_items', 'price_at_addition')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->decimal('price_at_addition', 10, 2)->after('quantity');
            });
        }
        
        // Update existing cart items with current product prices
        DB::statement('
            UPDATE cart_items ci
            JOIN products p ON ci.product_id = p.id
            SET ci.price_at_addition = p.price
        ');
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('price_at_addition');
        });
    }
};
