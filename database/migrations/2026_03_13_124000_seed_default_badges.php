<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            ['name' => 'Premier message', 'description' => 'Publie sa premiere reponse'],
            ['name' => 'Participant actif', 'description' => 'A publie 10 reponses'],
            ['name' => 'Createur', 'description' => 'A cree son premier sujet'],
            ['name' => 'Top contributeur', 'description' => 'A publie au moins 50 reponses'],
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
            'Premier message',
            'Participant actif',
            'Createur',
            'Top contributeur',
        ])->delete();
    }
};
