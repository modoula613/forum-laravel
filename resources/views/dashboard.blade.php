<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <p class="section-kicker">Vue d'ensemble</p>
                <h2 class="mt-3 text-4xl font-semibold text-stone-950">Tableau de bord</h2>
                <p class="muted-copy mt-3 text-base leading-7">
                    Retrouve en un coup d'oeil l'activite du forum Sphere et accede rapidement aux conversations.
                </p>
            </div>
            <a
                href="{{ route('topics.index') }}"
                class="inline-flex items-center rounded-full bg-[var(--brand)] px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-white shadow-[0_18px_35px_rgba(79,70,229,0.28)] transition hover:-translate-y-0.5 hover:bg-[var(--brand-deep)]"
            >
                Voir les sujets
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-5 md:grid-cols-3">
                <article class="glass-panel-strong rounded-[2rem] p-6">
                    <p class="text-sm font-medium text-stone-500">Sujets</p>
                    <p class="mt-3 text-4xl font-semibold text-stone-950">{{ \App\Models\Topic::count() }}</p>
                    <p class="muted-copy mt-3 text-sm">Nombre total de discussions ouvertes sur Sphere.</p>
                </article>

                <article class="glass-panel-strong rounded-[2rem] p-6">
                    <p class="text-sm font-medium text-stone-500">Reponses</p>
                    <p class="mt-3 text-4xl font-semibold text-stone-950">{{ \App\Models\Reply::count() }}</p>
                    <p class="muted-copy mt-3 text-sm">Messages publies par la communaute.</p>
                </article>

                <article class="glass-panel-strong rounded-[2rem] p-6">
                    <p class="text-sm font-medium text-stone-500">Membres</p>
                    <p class="mt-3 text-4xl font-semibold text-stone-950">{{ \App\Models\User::count() }}</p>
                    <p class="muted-copy mt-3 text-sm">Utilisateurs inscrits sur le forum.</p>
                </article>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <section class="glass-panel rounded-[2.25rem] p-6 sm:p-8">
                    <p class="section-kicker">Activite</p>
                    <h3 class="mt-3 text-3xl font-semibold text-stone-950">Le forum reste vivant</h3>
                    <p class="muted-copy mt-4 max-w-2xl text-base leading-8">
                        Utilise ce tableau de bord pour suivre les grands volumes du forum avant de replonger dans les sujets recents, les reponses actives et les notifications.
                    </p>
                </section>

                <aside class="rounded-[2.25rem] bg-[linear-gradient(135deg,var(--brand),var(--accent-soft))] p-6 text-white shadow-[0_20px_45px_rgba(79,70,229,0.24)] sm:p-8">
                    <p class="text-sm font-medium text-white/75">Raccourci</p>
                    <h3 class="mt-3 text-3xl font-semibold">Continue les discussions</h3>
                    <p class="mt-4 text-base leading-8 text-white/85">
                        Retourne sur la liste des sujets pour publier, rechercher ou trier les conversations les plus actives.
                    </p>
                    <a
                        href="{{ route('topics.index') }}"
                        class="mt-6 inline-flex items-center rounded-full border border-white/20 bg-white/15 px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-white transition hover:bg-white/20"
                    >
                        Ouvrir le forum
                    </a>
                </aside>
            </div>

            @php
                $recommendedTopics = auth()->user()->followedTags()->exists()
                    ? \App\Models\Topic::whereHas('tags', fn ($query) => $query->whereIn('tags.id', auth()->user()->followedTags->pluck('id')))
                        ->where('is_draft', false)
                        ->with(['user', 'tags'])
                        ->latest()
                        ->take(10)
                        ->get()
                    : collect();
            @endphp

            <section class="glass-panel rounded-[2.25rem] p-6 sm:p-8">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="section-kicker">Recommandations</p>
                        <h3 class="mt-3 text-3xl font-semibold text-stone-950">Sujets recommandes pour vous</h3>
                    </div>
                    <a href="{{ route('topics.feed') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-white">
                        Ouvrir mon flux
                    </a>
                </div>
                <div class="mt-6 grid gap-4">
                    @forelse ($recommendedTopics as $topic)
                        <article class="rounded-[1.5rem] bg-white/70 p-5">
                            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-700">Recommande</span>
                                <span>{{ $topic->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <h4 class="mt-3 text-xl font-semibold text-stone-950">
                                <a href="{{ route('topics.show', $topic) }}" class="transition hover:text-[var(--brand-deep)]">
                                    {{ $topic->title }}
                                </a>
                            </h4>
                            <p class="mt-2 text-sm text-stone-500">
                                Par <span class="font-semibold text-stone-700">{{ $topic->user->name }}</span>
                            </p>
                            @if ($topic->tags->isNotEmpty())
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach ($topic->tags as $tag)
                                        <a href="{{ route('tags.show', $tag) }}" class="rounded-full bg-[rgba(79,70,229,0.1)] px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[var(--brand)] transition hover:bg-[rgba(79,70,229,0.16)]">
                                            {{ $tag->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    @empty
                        <div class="rounded-[1.5rem] bg-white/70 p-6 text-sm text-stone-500">
                            Suivez des tags pour voir ici les 10 sujets les plus recents qui vous correspondent.
                        </div>
                    @endforelse
                </div>
            </section>

            @php
                $recentBookmarkedReplies = auth()->user()
                    ->bookmarkedReplies()
                    ->with(['topic', 'user'])
                    ->latest('reply_bookmarks.created_at')
                    ->take(5)
                    ->get();
            @endphp

            <section class="glass-panel rounded-[2.25rem] p-6 sm:p-8">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="section-kicker">Signets</p>
                        <h3 class="mt-3 text-3xl font-semibold text-stone-950">Reponses sauvegardees recentes</h3>
                    </div>
                    <a href="{{ route('replies.bookmarks') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-white">
                        Voir toutes les sauvegardes
                    </a>
                </div>
                <div class="mt-6 grid gap-4">
                    @forelse ($recentBookmarkedReplies as $reply)
                        <article class="rounded-[1.5rem] bg-white/70 p-5">
                            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">
                                <span class="rounded-full bg-[rgba(79,70,229,0.12)] px-3 py-1 text-[var(--brand)]">Reponse</span>
                                @if ($reply->pivot?->created_at)
                                    <span>Sauvegardee le {{ $reply->pivot->created_at->format('d/m/Y H:i') }}</span>
                                @endif
                            </div>
                            <h4 class="mt-3 text-xl font-semibold text-stone-950">
                                <a href="{{ route('topics.show', $reply->topic) }}" class="transition hover:text-[var(--brand-deep)]">
                                    {{ $reply->topic->title }}
                                </a>
                            </h4>
                            <p class="mt-2 text-sm text-stone-500">
                                Par <span class="font-semibold text-stone-700">{{ $reply->user->name }}</span>
                            </p>
                            <p class="mt-3 text-sm leading-7 text-stone-600">
                                {{ \Illuminate\Support\Str::limit($reply->content, 180) }}
                            </p>
                        </article>
                    @empty
                        <div class="rounded-[1.5rem] bg-white/70 p-6 text-sm text-stone-500">
                            Aucune reponse sauvegardee pour le moment.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
