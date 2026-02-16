<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change kyc_status ENUM to include all needed values
        DB::statement("ALTER TABLE users MODIFY kyc_status ENUM('unverified', 'pending', 'verified', 'rejected') DEFAULT 'unverified'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to previous ENUM (if known, otherwise keep as-is)
        DB::statement("ALTER TABLE users MODIFY kyc_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending'");
    }
};
