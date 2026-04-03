<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Actualites et debats',
                'description' => 'Discussions sur l’actualite, les grands sujets de societe, les tendances et les debats du moment.',
            ],
            [
                'name' => 'Culture et loisirs',
                'description' => 'Films, series, musique, livres, jeux video, sorties et tout ce qui nourrit les passions du quotidien.',
            ],
            [
                'name' => 'Sport',
                'description' => 'Football, basketball, fitness, sports de combat, performances, resultats et discussions entre supporters.',
            ],
            [
                'name' => 'Voyages et decouvertes',
                'description' => 'Destinations, conseils pratiques, recits de voyage, bons plans et envies d’evasion.',
            ],
            [
                'name' => 'Cuisine et gourmandise',
                'description' => 'Recettes, techniques, produits, restaurants, cuisine maison et plaisirs a partager.',
            ],
            [
                'name' => 'Mode de vie et bien-etre',
                'description' => 'Habitudes, sante, organisation personnelle, motivation, equilibre de vie et bien-etre au quotidien.',
            ],
            [
                'name' => 'Maison et quotidien',
                'description' => 'Organisation, deco, jardin, bricolage, vie de famille et astuces de tous les jours.',
            ],
            [
                'name' => 'Technologie et numerique',
                'description' => 'Applications, outils, reseaux sociaux, usages du numerique, materiel et questions tech grand public.',
            ],
            [
                'name' => 'Etudes et travail',
                'description' => 'Orientation, etudes, emploi, organisation professionnelle, reconversion et partage d’experiences.',
            ],
            [
                'name' => 'Argent et projets',
                'description' => 'Budget, achats, entrepreneuriat, projets personnels, bons plans et gestion concrete du quotidien.',
            ],
            [
                'name' => 'Relations et vie sociale',
                'description' => 'Amitie, couple, vie sociale, communication, situations du quotidien et conseils entre membres.',
            ],
            [
                'name' => 'Creativite et passions',
                'description' => 'Photographie, ecriture, dessin, artisanat, creation de contenu et hobbies creatifs.',
            ],
            [
                'name' => 'Questions et entraide',
                'description' => 'Besoin d’un avis, d’un conseil ou d’un coup de main sur un sujet du quotidien ? Cette categorie sert a s’entraider.',
            ],
            [
                'name' => 'Petites annonces et bons plans',
                'description' => 'Partage de trouvailles utiles, recommandations, ventes, opportunites et bons plans de la communaute.',
            ],
            [
                'name' => 'Hors sujet',
                'description' => 'Discussions legeres, sujets improbables, conversations spontanees et moments plus detendus.',
            ],
        ];

        $existingCategories = Category::orderBy('id')->get();

        foreach ($categories as $index => $data) {
            $slug = Str::slug($data['name']);
            $existing = $existingCategories[$index] ?? null;

            if ($existing) {
                $existing->update([
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'slug' => $slug,
                ]);

                continue;
            }

            Category::create($data + ['slug' => $slug]);
        }

        if ($existingCategories->count() > count($categories)) {
            $existingCategories
                ->slice(count($categories))
                ->filter(fn (Category $category) => $category->topics()->count() === 0)
                ->each
                ->delete();
        }
    }
}
