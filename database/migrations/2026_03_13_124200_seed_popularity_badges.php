<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            ['name' => 'Sujet populaire', 'description' => 'Auteur d un sujet ayant atteint 20 reponses'],
            ['name' => 'Utilisateur apprecie', 'description' => 'A recu au moins 25 likes sur ses reponses'],
        ] as $badge) {
            $existing = DB::table('badges')->where('name', $badge['name'])->first();

            if ($existing) {
                DB::table('badges')
                    ->where('id', $existing->id)
                    ->update([
                        'description' => $badge['description'],
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('badges')->insert([
                    'name' => $badge['name'],
                    'description' => $badge['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('badges')->whereIn('name', [
            'Sujet populaire',
            'Utilisateur apprecie',
        ])->delete();
    }
};
