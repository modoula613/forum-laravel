<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\NewsArticle;
use App\Models\Topic;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SeedConversationTopicsCommand extends Command
{
    protected $signature = 'forum:seed-conversations {email=modoula.elbou@hotmail.com} {--count=18}';

    protected $description = 'Cree des sujets de discussion naturels pour donner de la vie au forum.';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('Aucun utilisateur trouve pour cet email.');

            return self::FAILURE;
        }

        $targetCount = max(1, (int) $this->option('count'));
        $created = 0;

        $newsArticles = NewsArticle::with('category')
            ->latest('published_at')
            ->take($targetCount)
            ->get();

        foreach ($this->buildNewsConversations($newsArticles) as $topicData) {
            if ($created >= $targetCount) {
                break;
            }

            if ($this->createTopicIfMissing($user, $topicData)) {
                $created++;
            }
        }

        if ($created < $targetCount) {
            foreach ($this->buildCommunityConversations($targetCount - $created) as $topicData) {
                if ($this->createTopicIfMissing($user, $topicData)) {
                    $created++;
                }
            }
        }

        $this->info($created.' sujet(s) cree(s).');

        return self::SUCCESS;
    }

    protected function buildNewsConversations(Collection $articles): Collection
    {
        $openers = [
            'Vous en pensez quoi : %s ?',
            'On en parle deux minutes : %s',
            'Je suis le seul a bloquer sur ca : %s',
            'Vrai sujet ou simple buzz : %s ?',
            'Ca vous inquiete aussi ou pas du tout : %s ?',
            'J’ai vu passer ca et je me pose une vraie question : %s',
        ];

        $reactions = [
            'je trouve que le sujet risque de prendre encore plus de place dans les prochains jours.',
            'j’ai l’impression que tout le monde reagit a chaud sans vraiment prendre du recul.',
            'je ne sais pas si on mesure deja les consequences concretes derriere cette info.',
            'franchement, je comprends qu’on en parle autant, il y a quand meme un vrai impact derriere.',
            'je suis partage, parce qu’entre le titre et la realite, il y a parfois un monde.',
        ];

        $questions = [
            'Vous voyez plutot une tendance de fond ou juste une grosse actualite du moment ?',
            'Vous pensez que ca va encore peser dans quelques semaines ou ca va retomber vite ?',
            'Si vous deviez retenir une consequence concrete, ce serait laquelle ?',
            'Vous trouvez qu’on dramatise ou au contraire qu’on sous-estime encore le sujet ?',
            'Il y a un angle que vous trouvez trop peu discute dans tout ca ?',
        ];

        return $articles->map(function (NewsArticle $article, int $index) use ($openers, $reactions, $questions) {
            $headline = Str::of($article->title)
                ->replaceMatches('/^EN DIRECT,?\s*/iu', '')
                ->trim()
                ->limit(92, '...')
                ->value();

            $title = sprintf($openers[$index % count($openers)], $headline);

            $excerpt = Str::of($article->excerpt ?: $article->content ?: '')
                ->squish()
                ->limit(220, '...')
                ->value();

            $content = collect([
                'Je viens de voir passer cette actu'.($article->source_name ? ' sur '.$article->source_name : '').' et '.$reactions[$index % count($reactions)],
                $excerpt ? 'En gros : '.$excerpt : null,
                $questions[$index % count($questions)],
                $article->source_url ? 'Lien si vous voulez lire la source : '.$article->source_url : null,
            ])->filter()->implode("\n\n");

            return [
                'title' => $title,
                'content' => $content,
                'category_id' => $article->category_id,
            ];
        });
    }

    protected function buildCommunityConversations(int $count): Collection
    {
        $categories = Category::orderBy('name')->get();

        $fallbacks = [
            [
                'title' => 'Vous ouvrez l’appli info combien de fois par jour, en vrai ?',
                'content' => "Je me rends compte que j’ouvre les actus beaucoup plus souvent qu’avant, parfois sans meme m’en rendre compte.\n\nVous avez reussi a garder une distance saine avec tout ca ou pas du tout ?",
                'category_slug' => 'actualites-et-debats',
            ],
            [
                'title' => 'Le sujet dont tout le monde parle en ce moment, vous en pensez quoi ?',
                'content' => "J’ai l’impression qu’il y a toujours une grosse discussion qui prend toute la place pendant quelques jours.\n\nVous aimez suivre ces debats ou au contraire vous saturez vite ?",
                'category_slug' => 'actualites-et-debats',
            ],
            [
                'title' => 'C’est quoi votre rituel du matin pour demarrer la journee ?',
                'content' => "Cafe, musique, silence, sport, scroll du telephone...\n\nJe suis curieux de voir les petites habitudes qui changent vraiment une journee.",
                'category_slug' => 'mode-de-vie-et-bien-etre',
            ],
            [
                'title' => 'Vous regardez encore la tele ou tout passe par le tel maintenant ?',
                'content' => "Entre les reseaux, les applis et les plateformes, j’ai l’impression que la tele classique a completement perdu sa place chez beaucoup de monde.\n\nChez vous, ca se passe comment ?",
                'category_slug' => 'culture-et-loisirs',
            ],
            [
                'title' => 'Le meilleur bon plan que vous avez trouve cette annee ?',
                'content' => "Pas forcement un truc enorme, juste le genre de decouverte qu’on est content d’avoir faite.\n\nCa peut etre un service, une appli, une habitude ou meme une adresse.",
                'category_slug' => 'petites-annonces-et-bons-plans',
            ],
            [
                'title' => 'Vous avez encore confiance dans ce qu’on lit sur internet ou vous doutez de tout ?',
                'content' => "Entre les titres racoleurs, les posts sortis de leur contexte et les reactions a chaud, je trouve que c’est de plus en plus dur de se faire une idee claire.\n\nVous verifiez encore beaucoup ou vous faites au feeling ?",
                'category_slug' => 'questions-et-entraide',
            ],
        ];

        return collect($fallbacks)
            ->take($count)
            ->map(function (array $topic) use ($categories) {
                return [
                    'title' => $topic['title'],
                    'content' => $topic['content'],
                    'category_id' => $categories->firstWhere('slug', $topic['category_slug'])?->id,
                ];
            });
    }

    protected function createTopicIfMissing(User $user, array $topicData): bool
    {
        $existing = Topic::where('title', $topicData['title'])->first();

        if ($existing) {
            return false;
        }

        $topic = $user->topics()->create([
            'title' => $topicData['title'],
            'content' => $topicData['content'],
            'category_id' => $topicData['category_id'] ?? null,
            'is_draft' => false,
        ]);

        UserActivity::create([
            'user_id' => $user->id,
            'type' => 'topic_created',
            'description' => 'A cree un sujet : '.$topic->title,
        ]);

        return true;
    }
}
