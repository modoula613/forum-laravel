<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Str;

class NewsCategorizer
{
    public function resolveCategoryId(array $article): ?int
    {
        $text = Str::lower(trim(
            ($article['title'] ?? '').' '.
            ($article['description'] ?? '').' '.
            ($article['content'] ?? '')
        ));

        $keywordsByCategory = [
            'Actualites et debats' => ['politique', 'gouvernement', 'election', 'president', 'conflit', 'loi', 'international', 'crise', 'manifestation', 'debat'],
            'Culture et loisirs' => ['film', 'serie', 'musique', 'concert', 'festival', 'livre', 'cinema', 'netflix', 'anime', 'jeu video'],
            'Sport' => ['sport', 'football', 'basket', 'tennis', 'ligue', 'match', 'coupe', 'athlete', 'olympique', 'rugby'],
            'Voyages et decouvertes' => ['voyage', 'tourisme', 'destination', 'vol', 'hotel', 'compagnie aerienne', 'sejour', 'paysage'],
            'Cuisine et gourmandise' => ['cuisine', 'recette', 'restaurant', 'chef', 'gastronomie', 'alimentaire', 'boisson', 'vin'],
            'Mode de vie et bien-etre' => ['sante', 'bien-etre', 'stress', 'sommeil', 'habitude', 'forme', 'meditation', 'psychologie'],
            'Maison et quotidien' => ['maison', 'logement', 'deco', 'bricolage', 'jardin', 'famille', 'enfant', 'quotidien'],
            'Technologie et numerique' => ['technologie', 'smartphone', 'application', 'reseau social', 'internet', 'ia', 'apple', 'google', 'microsoft', 'numerique'],
            'Etudes et travail' => ['emploi', 'travail', 'etude', 'universite', 'ecole', 'carriere', 'bureau', 'entreprise', 'recrutement'],
            'Argent et projets' => ['economie', 'argent', 'budget', 'entreprise', 'startup', 'investissement', 'immobilier', 'projet', 'marche'],
            'Relations et vie sociale' => ['couple', 'amour', 'amitie', 'relation', 'famille', 'societe', 'communication'],
            'Creativite et passions' => ['photo', 'photographie', 'dessin', 'art', 'creation', 'ecriture', 'artisanat', 'passion'],
            'Questions et entraide' => ['conseil', 'astuce', 'probleme', 'question', 'entraide', 'aide', 'solution'],
            'Petites annonces et bons plans' => ['promo', 'bon plan', 'reduction', 'offre', 'vente', 'occasion', 'annonce'],
            'Hors sujet' => [],
        ];

        foreach ($keywordsByCategory as $categoryName => $keywords) {
            foreach ($keywords as $keyword) {
                if (Str::contains($text, $keyword)) {
                    return Category::where('name', $categoryName)->value('id')
                        ?? Category::where('name', 'Actualites et debats')->value('id');
                }
            }
        }

        return Category::where('name', 'Actualites et debats')->value('id')
            ?? Category::query()->value('id');
    }
}
