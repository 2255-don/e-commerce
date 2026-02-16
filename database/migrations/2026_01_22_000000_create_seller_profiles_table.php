<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->string('shop_name');
            $table->string('business_name')->nullable();
            $table->string('license_number')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(10.00);
            
            // License & Status dates
            $table->timestamp('licence_paid_at')->nullable();
            $table->timestamp('licence_expire_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            
            // Reasons
            $table->text('rejection_reason')->nullable();
            $table->text('suspension_reason')->nullable();
            
            $table->boolean('is_active')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_profiles');
    }
};
