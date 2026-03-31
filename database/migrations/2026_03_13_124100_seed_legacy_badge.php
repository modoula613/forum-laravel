<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $existing = DB::table('badges')->where('name', 'Utilisateur ancien')->first();

        if ($existing) {
            DB::table('badges')
                ->where('id', $existing->id)
                ->update([
                    'description' => 'Inscrit depuis plus d un an',
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('badges')->insert([
                'name' => 'Utilisateur ancien',
                'description' => 'Inscrit depuis plus d un an',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('badges')
            ->where('name', 'Utilisateur ancien')
            ->delete();
    }
};
