<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Reply;
use App\Models\Topic;
use App\Models\User;
use App\Models\UserActivity;
use Carbon\CarbonImmutable;
use Database\Seeders\CategorySeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SeedDemoCommunityCommand extends Command
{
    protected $signature = 'forum:seed-demo-community';

    protected $description = 'Cree une dizaine de faux utilisateurs avec des discussions naturelles.';

    public function handle(): int
    {
        if (Category::count() === 0) {
            app(CategorySeeder::class)->run();
        }

        $users = collect($this->demoUsers())->mapWithKeys(function (array $profile) {
            $user = User::firstOrCreate(
                ['email' => $profile['email']],
                [
                    'name' => $profile['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(32)),
                    'role' => 'user',
                    'warning_count' => 0,
                    'is_blocked' => false,
                    'is_banned' => false,
                    'level' => $profile['level'],
                    'experience' => $profile['experience'],
                    'reputation' => $profile['reputation'],
                ]
            );

            $user->forceFill([
                'name' => $profile['name'],
                'role' => 'user',
                'warning_count' => 0,
                'is_blocked' => false,
                'is_banned' => false,
                'level' => $profile['level'],
                'experience' => $profile['experience'],
                'reputation' => $profile['reputation'],
            ])->saveQuietly();

            return [$profile['email'] => $user];
        });

        $categories = Category::query()->get()->keyBy('slug');

        $createdTopics = 0;
        $createdReplies = 0;

        foreach ($this->demoConversations() as $conversation) {
            $author = $users->get($conversation['author']);
            $category = $categories->get($conversation['category']);

            if (! $author || ! $category) {
                continue;
            }

            $topicTime = CarbonImmutable::now()->subHours($conversation['hours_ago']);

            $emoji = $conversation['emoji'] ?? null;
            $title = $emoji ? "{$emoji} {$conversation['title']}" : $conversation['title'];
            $content = $this->embellishTopicContent($conversation['content'], $emoji);
            $lookupTitles = collect([$conversation['title'], $title, $conversation['lookup_title'] ?? null])
                ->filter()
                ->unique()
                ->values();

            $topic = Topic::query()
                ->where('user_id', $author->id)
                ->whereIn('title', $lookupTitles)
                ->first();

            if (! $topic) {
                $topic = new Topic([
                    'title' => $title,
                    'content' => $content,
                    'category_id' => $category->id,
                    'is_draft' => false,
                    'is_locked' => false,
                    'is_pinned' => false,
                ]);
                $topic->user()->associate($author);
                $topic->save();
                $createdTopics++;
            } else {
                $topic->forceFill([
                    'title' => $title,
                    'content' => $content,
                    'category_id' => $category->id,
                    'is_draft' => false,
                    'is_locked' => false,
                    'is_pinned' => false,
                ])->saveQuietly();
            }

            $topic->forceFill([
                'created_at' => $topicTime,
                'updated_at' => $topicTime,
            ])->saveQuietly();

            $this->createActivity($author, 'topic_created', 'A cree un sujet : '.$topic->title, $topicTime);

            $lastActivityAt = $topicTime;

            foreach ($conversation['replies'] as $index => $replyData) {
                $replyAuthor = $users->get($replyData['author']);

                if (! $replyAuthor) {
                    continue;
                }

                $replyTime = $topicTime->addMinutes(($index + 1) * 18);

                $reply = Reply::firstOrCreate(
                    [
                        'topic_id' => $topic->id,
                        'user_id' => $replyAuthor->id,
                        'content' => $replyData['content'],
                    ]
                );

                if ($reply->wasRecentlyCreated) {
                    $createdReplies++;
                }

                $reply->forceFill([
                    'created_at' => $replyTime,
                    'updated_at' => $replyTime,
                ])->saveQuietly();

                $this->createActivity($replyAuthor, 'reply_created', 'A repondu a : '.$topic->title, $replyTime);

                $lastActivityAt = $replyTime;
            }

            $topic->forceFill([
                'updated_at' => $lastActivityAt,
            ])->saveQuietly();
        }

        $this->info($users->count().' faux utilisateurs disponibles.');
        $this->info($createdTopics.' sujet(s) ajoutes.');
        $this->info($createdReplies.' reponse(s) ajoutees.');

        return self::SUCCESS;
    }

    protected function createActivity(User $user, string $type, string $description, CarbonImmutable $createdAt): void
    {
        $activity = UserActivity::firstOrCreate([
            'user_id' => $user->id,
            'type' => $type,
            'description' => $description,
        ]);

        $activity->forceFill([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ])->saveQuietly();
    }

    protected function demoUsers(): array
    {
        return [
            ['name' => 'Lina Benali', 'email' => 'lina@forum-demo.test', 'level' => 8, 'experience' => 220, 'reputation' => 148],
            ['name' => 'Mehdi Roux', 'email' => 'mehdi@forum-demo.test', 'level' => 6, 'experience' => 180, 'reputation' => 102],
            ['name' => 'Sarah Naji', 'email' => 'sarah@forum-demo.test', 'level' => 7, 'experience' => 245, 'reputation' => 131],
            ['name' => 'Karim El Idrissi', 'email' => 'karim@forum-demo.test', 'level' => 5, 'experience' => 120, 'reputation' => 88],
            ['name' => 'Julie Martin', 'email' => 'julie@forum-demo.test', 'level' => 9, 'experience' => 305, 'reputation' => 176],
            ['name' => 'Amina Farah', 'email' => 'amina@forum-demo.test', 'level' => 6, 'experience' => 130, 'reputation' => 95],
            ['name' => 'Nabil Othmani', 'email' => 'nabil@forum-demo.test', 'level' => 4, 'experience' => 70, 'reputation' => 61],
            ['name' => 'Salma Rahmani', 'email' => 'salma@forum-demo.test', 'level' => 5, 'experience' => 145, 'reputation' => 83],
            ['name' => 'Thomas Vasseur', 'email' => 'thomas@forum-demo.test', 'level' => 7, 'experience' => 190, 'reputation' => 124],
            ['name' => 'Ines Boulahri', 'email' => 'ines@forum-demo.test', 'level' => 5, 'experience' => 110, 'reputation' => 77],
        ];
    }

    protected function demoConversations(): array
    {
        return [
            [
                'author' => 'lina@forum-demo.test',
                'category' => 'actualites-et-debats',
                'emoji' => '📰',
                'hours_ago' => 54,
                'title' => 'Vous laissez encore les notifs info allumees sur votre tel ?',
                'content' => "Je me rends compte que des que je coupe les alertes info je me sens mieux, mais j'ai aussi peur de rater un sujet important.\n\nVous les laissez actives toute la journee ou vous avez fait le tri depuis longtemps ?",
                'replies' => [
                    ['author' => 'mehdi@forum-demo.test', 'content' => "J'ai garde seulement une appli et encore, en silencieux. Sinon j'ai l'impression d'etre en tension toute la journee."],
                    ['author' => 'julie@forum-demo.test', 'content' => "Je coupe tout le soir. Franchement ca change vraiment la qualite du sommeil chez moi."],
                    ['author' => 'karim@forum-demo.test', 'content' => "Le pire c'est les notifications qui te donnent juste un titre anxiogane sans contexte."],
                ],
            ],
            [
                'author' => 'julie@forum-demo.test',
                'category' => 'culture-et-loisirs',
                'emoji' => '🎫',
                'hours_ago' => 50,
                'title' => 'Le prix des places de concert, on en parle ou on a juste accepte ?',
                'content' => "Je regardais pour un concert ce matin et j'ai l'impression que sortir voir un artiste connu devient un luxe.\n\nVous continuez a y aller quand meme ou vous avez calmement laisse tomber ?",
                'replies' => [
                    ['author' => 'sarah@forum-demo.test', 'content' => "Je choisis beaucoup plus maintenant. Avant je prenais des places sur un coup de tete, aujourd'hui impossible."],
                    ['author' => 'thomas@forum-demo.test', 'content' => "Le pire c'est quand tu vois le prix de base puis les frais qui tombent a la fin."],
                    ['author' => 'salma@forum-demo.test', 'content' => "J'essaie les petites salles maintenant. Moins cher et souvent meilleure ambiance."],
                ],
            ],
            [
                'author' => 'karim@forum-demo.test',
                'category' => 'sport',
                'emoji' => '⚽',
                'hours_ago' => 47,
                'title' => 'Regarder un match sans ouvrir les reseaux, vous y arrivez encore ?',
                'content' => "Je voulais juste profiter du match tranquille hier et au final je lisais plus les reactions que je regardais l'ecran.\n\nVous arrivez encore a vivre un match sans deuxieme ecran ou c'est fini pour tout le monde ?",
                'replies' => [
                    ['author' => 'nabil@forum-demo.test', 'content' => "Impossible pour moi pendant un gros match, j'aime trop voir les reactions a chaud."],
                    ['author' => 'lina@forum-demo.test', 'content' => "Ca depend. Quand l'enjeu est fort j'evite, sinon je me fais spoiler par les tweets avant meme de voir l'action."],
                    ['author' => 'mehdi@forum-demo.test', 'content' => "Le live est parfois plus drole que le match, donc je comprends le piege."],
                ],
            ],
            [
                'author' => 'amina@forum-demo.test',
                'category' => 'mode-de-vie-et-bien-etre',
                'emoji' => '🌙',
                'hours_ago' => 43,
                'title' => 'Vous arrivez vraiment a couper les ecrans avant de dormir ?',
                'content' => "Je me dis tous les soirs que je vais poser le tel une heure avant de dormir, et tous les soirs je finis par scroller encore un peu.\n\nVous avez trouve une astuce qui marche vraiment ou c'est juste une lutte quotidienne ?",
                'replies' => [
                    ['author' => 'ines@forum-demo.test', 'content' => "Le seul truc qui m'aide c'est de charger le tel loin du lit. Sinon je craque toujours."],
                    ['author' => 'julie@forum-demo.test', 'content' => "J'ai remplace le scroll par de la lecture papier. Ca ne marche pas tous les soirs mais ca aide."],
                    ['author' => 'thomas@forum-demo.test', 'content' => "Perso je mets un minuteur sur les applis, mais bon je triche parfois."],
                ],
            ],
            [
                'author' => 'thomas@forum-demo.test',
                'category' => 'etudes-et-travail',
                'emoji' => '🧠',
                'hours_ago' => 39,
                'title' => 'Les reunions qui auraient pu etre un message, on fait comment ?',
                'content' => "Je sors encore d'un point de 45 minutes pour deux infos qu'on aurait pu ecrire en trois lignes.\n\nDans vos tafs aussi ca prend autant de place ou j'ai juste pas de chance ?",
                'replies' => [
                    ['author' => 'mehdi@forum-demo.test', 'content' => "Chez nous c'est devenu une tradition presque. Le pire c'est la reunion pour preparer la prochaine reunion."],
                    ['author' => 'sarah@forum-demo.test', 'content' => "Quand l'ordre du jour n'existe pas, je sais deja que je vais perdre mon temps."],
                    ['author' => 'salma@forum-demo.test', 'content' => "Le teletravail a un peu arrange ca chez nous, mais pas completement."],
                ],
            ],
            [
                'author' => 'mehdi@forum-demo.test',
                'category' => 'technologie-et-numerique',
                'emoji' => '🎧',
                'hours_ago' => 35,
                'title' => 'Les vocaux de 3 minutes, pratique ou enfer ?',
                'content' => "J'ai l'impression qu'on remplace de plus en plus les messages ecrits par des vocaux ultra longs.\n\nVous trouvez ca plus humain ou juste impossible a gerer quand t'es dans les transports ou au boulot ?",
                'replies' => [
                    ['author' => 'amina@forum-demo.test', 'content' => "Si c'est pour raconter un truc perso je prefere le vocal. Mais pour une info simple, non merci."],
                    ['author' => 'nabil@forum-demo.test', 'content' => "Le vrai enfer c'est quand la personne envoie cinq vocaux a la suite au lieu d'un seul message."],
                    ['author' => 'lina@forum-demo.test', 'content' => "J'avoue que je les accelere toujours, sinon je n'en vois jamais la fin."],
                ],
            ],
            [
                'author' => 'salma@forum-demo.test',
                'category' => 'cuisine-et-gourmandise',
                'emoji' => '🍝',
                'hours_ago' => 31,
                'title' => 'Vous cuisinez encore le soir ou la flemme gagne souvent ?',
                'content' => "J'aime bien cuisiner mais en semaine j'ai souvent la flemme totale en rentrant.\n\nVous avez des repas faciles qui sauvent vos soirs sans finir systematiquement en livraison ?",
                'replies' => [
                    ['author' => 'karim@forum-demo.test', 'content' => "Les pates avec un vrai bon truc a cote, c'est mon mode survie et j'assume."],
                    ['author' => 'ines@forum-demo.test', 'content' => "Je fais des grosses portions le dimanche et je me remercie toute la semaine."],
                    ['author' => 'julie@forum-demo.test', 'content' => "Omelette salade pain. Pas glamour mais efficace."],
                ],
            ],
            [
                'author' => 'ines@forum-demo.test',
                'category' => 'voyages-et-decouvertes',
                'emoji' => '✈️',
                'hours_ago' => 28,
                'title' => 'Vous partez plutot en week-end improvise ou tout planifier des mois avant ?',
                'content' => "J'adore l'idee du depart sur un coup de tete, mais des que je dois reserver logement, train, budget, je redeviens ultra prudente.\n\nVous etes team improvisation ou team tableau Excel ?",
                'replies' => [
                    ['author' => 'amina@forum-demo.test', 'content' => "Pour un week-end court je pars au feeling, pour les grandes vacances je planifie presque tout."],
                    ['author' => 'thomas@forum-demo.test', 'content' => "J'aimerais etre spontanne mais mon compte bancaire me rappelle vite a l'ordre."],
                    ['author' => 'sarah@forum-demo.test', 'content' => "Le bon compromis c'est de reserver le gros et laisser le reste ouvert."],
                ],
            ],
            [
                'author' => 'sarah@forum-demo.test',
                'category' => 'relations-et-vie-sociale',
                'emoji' => '💬',
                'hours_ago' => 24,
                'title' => 'C est moi ou plus personne ne dit franchement quand quelque chose le derange ?',
                'content' => "J'ai l'impression qu'on contourne beaucoup les discussions directes, puis tout ressort d'un coup au pire moment.\n\nVous aussi vous voyez ca autour de vous ou c'est juste mon entourage qui fuit le conflit ?",
                'replies' => [
                    ['author' => 'lina@forum-demo.test', 'content' => "Je vois exactement ca. On garde tout, puis ca explose pour un detail."],
                    ['author' => 'thomas@forum-demo.test', 'content' => "Je pense qu'on veut tous paraitre cool, donc on repousse les conversations pas agreables."],
                    ['author' => 'julie@forum-demo.test', 'content' => "Dire les choses calmement au bon moment evite tellement de malentendus pourtant."],
                ],
            ],
            [
                'author' => 'nabil@forum-demo.test',
                'category' => 'questions-et-entraide',
                'emoji' => '🧩',
                'hours_ago' => 20,
                'title' => 'Comment vous faites pour rester concentres plus de 20 minutes ?',
                'content' => "Je commence une tache, puis je regarde un message, puis une notif, puis autre chose, et au final j'ai avance sur rien.\n\nJe prends toutes vos vraies astuces, meme les plus simples.",
                'replies' => [
                    ['author' => 'mehdi@forum-demo.test', 'content' => "Je mets le tel dans une autre piece. C'est brutal mais c'est le seul truc qui marche."],
                    ['author' => 'amina@forum-demo.test', 'content' => "Je me fixe un mini objectif de 15 minutes, juste pour demarrer. Le plus dur c'est souvent le debut."],
                    ['author' => 'salma@forum-demo.test', 'content' => "Le casque aide beaucoup chez moi, meme sans musique."],
                ],
            ],
            [
                'author' => 'julie@forum-demo.test',
                'category' => 'argent-et-projets',
                'emoji' => '💸',
                'hours_ago' => 16,
                'title' => 'Vous avez encore un vrai budget mensuel ou vous faites au feeling ?',
                'content' => "Pendant longtemps je regardais juste le solde en fin de mois, mais avec tout qui augmente je me dis que ce n'est plus possible.\n\nVous suivez vraiment vos depenses ou vous improvisez encore ?",
                'replies' => [
                    ['author' => 'karim@forum-demo.test', 'content' => "Je fais simple: loyer, courses, sorties, reste. Si je complexifie je tiens trois jours."],
                    ['author' => 'ines@forum-demo.test', 'content' => "Depuis que j'ai un plafond pour les achats impulsifs, j'ai beaucoup moins de regrets."],
                    ['author' => 'thomas@forum-demo.test', 'content' => "Le vrai changement c'est de regarder chaque semaine et pas juste a la fin du mois."],
                ],
            ],
            [
                'author' => 'amina@forum-demo.test',
                'category' => 'hors-sujet',
                'emoji' => '✨',
                'hours_ago' => 12,
                'title' => 'Votre petite habitude bizarre mais efficace du quotidien ?',
                'content' => "Je fais des listes sur des bouts de papier alors que tout existe sur le tel, mais c'est le seul format qui me calme vraiment.\n\nVous avez un petit truc du genre qui ne parait pas logique mais qui vous aide vraiment ?",
                'replies' => [
                    ['author' => 'sarah@forum-demo.test', 'content' => "Je range la piece avant de commencer a bosser, sinon je ne me sens pas en place."],
                    ['author' => 'nabil@forum-demo.test', 'content' => "Je marche cinq minutes avant chaque appel important. Ca m'evite de partir trop vite."],
                    ['author' => 'lina@forum-demo.test', 'content' => "Je me mets une musique precise quand je veux me motiver. Mon cerveau comprend direct."],
                ],
            ],
            [
                'author' => 'thomas@forum-demo.test',
                'category' => 'actualites-et-debats',
                'emoji' => '🏥',
                'hours_ago' => 10,
                'title' => 'Sur le cout reel des soins, vous voudriez savoir combien ca coute ou pas ?',
                'content' => "Je suis tombe sur un article qui posait la question et je me demande si ca aiderait vraiment les gens a mieux comprendre le systeme, ou si ca rajouterait surtout de l'angoisse.\n\nVous prefereriez savoir le vrai prix de chaque soin ou vous trouvez que ca compliquerait tout ?",
                'replies' => [
                    ['author' => 'julie@forum-demo.test', 'content' => "Moi je voudrais savoir, surtout pour me rendre compte de la valeur reelle des soins."],
                    ['author' => 'mehdi@forum-demo.test', 'content' => "Pourquoi pas, mais si c'est juste pour culpabiliser les gens, ca ne sert a rien."],
                    ['author' => 'karim@forum-demo.test', 'content' => "Le probleme c'est que tout le monde n'interprete pas une facture de la meme facon. Il faut du contexte avec."],
                ],
            ],
            [
                'author' => 'lina@forum-demo.test',
                'category' => 'technologie-et-numerique',
                'emoji' => '📱',
                'hours_ago' => 7,
                'title' => 'Les gens qui changent de telephone tous les ans, vous comprenez ?',
                'content' => "J'ai garde le mien presque quatre ans et j'ai du mal a voir ce qui justifie de changer tous les ans a part l'envie.\n\nVous trouvez ca normal si on en a les moyens ou juste du marketing bien rode ?",
                'replies' => [
                    ['author' => 'ines@forum-demo.test', 'content' => "Je comprends l'envie mais pas le besoin. Les differences sont de plus en plus petites."],
                    ['author' => 'nabil@forum-demo.test', 'content' => "Si ton tel rame tous les jours, je comprends. Sinon chaque annee, non."],
                    ['author' => 'salma@forum-demo.test', 'content' => "Le vrai sujet pour moi c'est surtout la batterie. Quand elle me lache je change."],
                ],
            ],
            [
                'author' => 'karim@forum-demo.test',
                'category' => 'petites-annonces-et-bons-plans',
                'emoji' => '🛍️',
                'hours_ago' => 4,
                'title' => 'Le meilleur petit achat a moins de 30 euros que vous avez fait cette annee ?',
                'content' => "Pas un gros achat, juste le truc utile du quotidien que vous ne regrettez pas.\n\nCa peut etre pour la maison, le boulot, le sport ou juste un detail qui change la vie.",
                'replies' => [
                    ['author' => 'amina@forum-demo.test', 'content' => "Une gourde isotherme. Je m'en sers tous les jours et j'aurais du la prendre avant."],
                    ['author' => 'thomas@forum-demo.test', 'content' => "Un support de tel pour le bureau. C'est bete mais ca m'a sauve plein d'appels."],
                    ['author' => 'julie@forum-demo.test', 'content' => "Des boites de rangement transparentes pour la cuisine. On dirait rien mais ca change tout."],
                ],
            ],
        ];
    }

    protected function embellishTopicContent(string $content, ?string $emoji): string
    {
        if (! $emoji) {
            return $content;
        }

        if (str_contains($content, "\n\n")) {
            return preg_replace('/\n\n/', " {$emoji}\n\n", $content, 1) ?? $content;
        }

        return "{$content} {$emoji}";
    }
}
