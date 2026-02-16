<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'role')) {
            return;
        }

        // Get the profil IDs
        $superAdminProfilId = DB::table('profils')->where('libelle', 'Super-Admin')->value('id');
        $utilisateurProfilId = DB::table('profils')->where('libelle', 'utilisateur')->value('id');

        // Migrate admin user to Super-Admin profile
        DB::table('users')
            ->where('role', 'admin')
            ->update(['profil_id' => $superAdminProfilId]);

        // Migrate all other users to utilisateur profile
        DB::table('users')
            ->where('role', '!=', 'admin')
            ->orWhereNull('role')
            ->update(['profil_id' => $utilisateurProfilId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore role field based on profil
        DB::table('users')
            ->join('profils', 'users.profil_id', '=', 'profils.id')
            ->where('profils.libelle', 'Super-Admin')
            ->update(['users.role' => 'admin']);

        DB::table('users')
            ->whereNotNull('profil_id')
            ->update(['profil_id' => null]);
    }
};
