<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignUuid('buyer_id')->constrained('users')->onDelete('cascade');
            
            $table->string('reference')->unique();
            $table->decimal('total_amount', 10, 2);
            
            $table->string('status')->default('pending');
            
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('pending');
            
            $table->string('delivery_status')->default('pending');
            $table->string('delivery_code')->nullable();
            
            $table->text('notes')->nullable();
            
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
