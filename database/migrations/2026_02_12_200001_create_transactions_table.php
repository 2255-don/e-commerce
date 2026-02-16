<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Wallets can be null for external transfers or system operations
            $table->foreignUuid('sender_wallet_id')->nullable()->constrained('wallets')->onDelete('set null');
            $table->foreignUuid('receiver_wallet_id')->nullable()->constrained('wallets')->onDelete('set null');
            
            $table->string('type'); // deposit, withdrawal, transfer, payment, refund
            $table->decimal('amount', 10, 2);
            $table->string('reference')->unique();
            $table->string('description')->nullable();
            
            $table->string('status')->default('pending'); // pending, processing, completed, failed, cancelled
            
            $table->json('metadata')->nullable();
            
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->string('failure_reason')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
