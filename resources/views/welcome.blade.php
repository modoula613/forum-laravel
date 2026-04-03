<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Sphere') }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|newsreader:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="ambient-page min-h-screen text-gray-900 antialiased">
        <div class="page-shell">
            <div class="mx-auto flex min-h-screen max-w-7xl flex-col px-6 py-6 lg:px-8">
            <header class="glass-panel flex items-center justify-between rounded-[2rem] px-6 py-5">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 text-xl font-semibold tracking-tight">
                    <span class="brand-dot"></span>
                    {{ config('app.name', 'Sphere') }}
                </a>
                <div class="flex items-center gap-3 text-sm">
                    <a href="{{ route('topics.index') }}" class="rounded-full px-4 py-2 font-medium text-stone-700 transition hover:bg-white/70">
                        Forum
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-full bg-stone-900 px-4 py-2 font-medium text-white transition hover:bg-[var(--brand-deep)]">
                            Tableau de bord
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full px-4 py-2 font-medium text-stone-700 transition hover:bg-white/70">
                            Connexion
                        </a>
                        <a href="{{ route('register') }}" class="rounded-full bg-stone-900 px-4 py-2 font-medium text-white transition hover:bg-[var(--brand-deep)]">
                            Inscription
                        </a>
                    @endauth
                </div>
            </header>

            <main class="grid flex-1 items-start gap-10 py-14 lg:grid-cols-[1.05fr_0.95fr] lg:py-20">
                <section class="relative">
                    <div class="glass-panel-strong max-w-4xl rounded-[2.5rem] p-8 sm:p-10 lg:p-12">
                        @if ($announcements->isNotEmpty())
                            <div class="mb-8 space-y-3">
                                @foreach ($announcements as $announcement)
                                    <div class="rounded-[1.5rem] border border-[rgba(79,70,229,0.12)] bg-[rgba(79,70,229,0.08)] px-5 py-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--brand)]">Annonce</p>
                                        <p class="mt-2 text-lg font-semibold text-stone-950">{{ $announcement->title }}</p>
                                        <p class="mt-2 text-sm leading-7 text-stone-600">{{ $announcement->content }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <p class="section-kicker">Forum communautaire</p>
                        <h1 class="hero-title mt-5 max-w-3xl text-stone-950">
                            Des conversations plus vivantes, dans une interface plus nette.
                        </h1>
                        <p class="muted-copy mt-6 max-w-2xl text-lg leading-8">
                            Publie un sujet, fais monter les idees utiles et garde des echanges lisibles sans surcharge visuelle. Le site met l'accent sur le contenu, pas sur le bruit.
                        </p>

                        <div class="mt-8 flex flex-wrap gap-4">
                            <a href="{{ route('topics.index') }}" class="inline-flex items-center rounded-full bg-[var(--brand)] px-6 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-white shadow-[0_18px_40px_rgba(184,92,56,0.22)] transition hover:-translate-y-0.5 hover:bg-[var(--brand-deep)]">
                                Explorer le forum
                            </a>
                            @guest
                                <a href="{{ route('register') }}" class="inline-flex items-center rounded-full border border-[rgba(90,60,40,0.14)] bg-white/70 px-6 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-stone-900 transition hover:bg-white">
                                    Ouvrir un compte
                                </a>
                            @endguest
                        </div>

                        <p class="mt-8 text-sm text-stone-500">
                            {{ $topicsCount }} sujets · {{ $repliesCount }} reponses · {{ $usersCount }} membres
                        </p>
                    </div>
                </section>

                <section class="grid gap-5">
                    <div class="glass-panel rounded-[2rem] p-6 sm:p-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="section-kicker">Entrer dans le forum</p>
                                <h2 class="mt-3 text-3xl font-semibold text-stone-950">Choisir un espace</h2>
                            </div>
                            <a href="{{ route('categories.index') }}" class="rounded-full bg-white/70 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-stone-700 transition hover:bg-white">
                                Voir tout
                            </a>
                        </div>
                        <div class="mt-5 grid gap-4">
                            @forelse ($categories as $category)
                                <a href="{{ route('categories.show', $category) }}" class="surface-outline rounded-[1.5rem] bg-white/70 p-5 transition hover:-translate-y-0.5 hover:bg-white">
                                    <div class="flex items-center justify-between gap-4">
                                        <div>
                                            <p class="text-xl font-semibold text-stone-950">{{ $category->name }}</p>
                                            <p class="mt-2 text-sm text-stone-500">{{ $category->topics_count }} sujet(s)</p>
                                            @if ($category->latestTopic)
                                                <p class="mt-2 text-sm leading-7 text-stone-600">
                                                    Dernier sujet: <span class="font-semibold text-stone-700">{{ \Illuminate\Support\Str::limit($category->latestTopic->title, 54) }}</span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="surface-outline rounded-[1.5rem] bg-white/70 p-5">
                                    <p class="text-sm text-stone-500">Les categories apparaitront ici des qu'elles seront creees.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="glass-panel rounded-[2rem] p-6 sm:p-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="section-kicker">Forum</p>
                                <h2 class="mt-3 text-3xl font-semibold text-stone-950">Discussions recentes</h2>
                            </div>
                            <a href="{{ route('topics.index') }}" class="rounded-full bg-white/70 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-stone-700 transition hover:bg-white">
                                Voir le forum
                            </a>
                        </div>
                        <div class="mt-5 space-y-3">
                            @forelse ($recentTopics as $topic)
                                <a href="{{ route('topics.show', $topic) }}" class="block rounded-[1.5rem] bg-white/70 px-5 py-4 transition hover:-translate-y-0.5 hover:bg-white">
                                    <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">
                                        @if ($topic->category)
                                            <span class="rounded-full bg-[rgba(20,184,166,0.12)] px-3 py-1 text-[var(--accent)]">
                                                {{ $topic->category->name }}
                                            </span>
                                        @endif
                                        <span>{{ $topic->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <p class="mt-3 text-lg font-semibold text-stone-950">{{ $topic->title }}</p>
                                    <p class="mt-2 text-sm text-stone-500">{{ $topic->user->name }}</p>
                                </a>
                            @empty
                                <div class="rounded-[1.5rem] bg-white/70 px-5 py-4 text-sm text-stone-500">
                                    Les nouveaux sujets apparaitront ici.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="glass-panel rounded-[2rem] p-6 sm:p-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="section-kicker">Actualites</p>
                                <h2 class="mt-3 text-3xl font-semibold text-stone-950">A la une</h2>
                            </div>
                            <a href="{{ route('news.index') }}" class="rounded-full bg-white/70 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-stone-700 transition hover:bg-white">
                                Voir tout
                            </a>
                        </div>
                        <div class="mt-5 space-y-3">
                            @forelse ($latestNews as $article)
                                <a href="{{ route('news.index', ['category' => $article->category?->slug]) }}" class="block rounded-[1.5rem] bg-white/70 px-5 py-4 transition hover:-translate-y-0.5 hover:bg-white">
                                        <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">
                                            @if ($article->category)
                                                <span class="rounded-full bg-[rgba(154,90,46,0.12)] px-3 py-1 text-[var(--accent)]">
                                                    {{ $article->category->name }}
                                                </span>
                                            @endif
                                            @if ($article->source_name)
                                                <span>{{ $article->source_name }}</span>
                                            @endif
                                        </div>
                                        <p class="mt-3 text-lg font-semibold text-stone-950">{{ $article->title }}</p>
                                        @if ($article->excerpt)
                                            <p class="mt-2 text-sm text-stone-500">{{ \Illuminate\Support\Str::limit($article->excerpt, 110) }}</p>
                                        @endif
                                </a>
                            @empty
                                <div class="rounded-[1.5rem] bg-white/70 px-5 py-4 text-sm text-stone-500">
                                    Les actualites importeront ici des qu’une synchronisation sera lancee.
                                </div>
                            @endforelse
                        </div>
                    </div>

                </section>
            </main>
            </div>
        </div>
    </body>
</html>
