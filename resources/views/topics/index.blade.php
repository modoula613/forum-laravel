<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <p class="section-kicker">Discussions</p>
                <h2 class="mt-3 text-4xl font-semibold text-stone-950">Le coeur du forum</h2>
                <p class="muted-copy mt-3 text-base leading-7">
                    Parcours les conversations, retrouve les sujets actifs et entre directement dans les echanges qui comptent.
                </p>
            </div>
            @auth
                @if (! auth()->user()->is_blocked)
                    <a
                        href="{{ route('topics.create') }}"
                        class="inline-flex items-center rounded-full bg-[var(--brand)] px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-white shadow-[0_18px_35px_rgba(79,70,229,0.28)] transition hover:-translate-y-0.5 hover:bg-[var(--brand-deep)]"
                    >
                        Nouveau sujet
                    </a>
                @endif
            @endauth
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-8 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="glass-panel rounded-[1.5rem] border-emerald-200 bg-emerald-50/90 px-5 py-4 text-sm text-emerald-900">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="glass-panel rounded-[1.5rem] border-rose-200 bg-rose-50/90 px-5 py-4 text-sm text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            @auth
                @if (auth()->user()->is_blocked)
                    <div class="glass-panel rounded-[2rem] border-rose-200 bg-rose-50/90 p-6 text-sm font-medium text-rose-800">
                        Votre compte est bloque suite a plusieurs infractions.
                    </div>
                @endif
                @if (request('recommended'))
                    <div class="glass-panel rounded-[2rem] border-amber-200 bg-amber-50/90 p-6 text-sm font-medium text-amber-800">
                        Affichage des sujets recommandes selon les tags que vous suivez.
                    </div>
                @endif
            @endauth

            @guest
                <div class="glass-panel rounded-[2rem] p-6">
                    <p class="text-sm text-stone-600">
                        Connecte-toi pour creer un sujet ou repondre.
                        <a href="{{ route('login') }}" class="font-semibold text-stone-900 underline underline-offset-4">Se connecter</a>
                    </p>
                </div>
            @endguest

            <div class="glass-panel rounded-[2rem] p-5 sm:p-6">
                <form method="GET" action="{{ route('topics.index') }}" class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="grid gap-4 sm:grid-cols-[1fr_220px] lg:w-full xl:grid-cols-[220px_1fr_220px_220px]">
                        <div>
                            <label for="category" class="mb-2 block text-sm font-semibold uppercase tracking-[0.16em] text-stone-600">
                                Categorie
                            </label>
                            <select
                                id="category"
                                name="category"
                                class="block w-full rounded-[1.25rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                            >
                                <option value="">Toutes les categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="tag" class="mb-2 block text-sm font-semibold uppercase tracking-[0.16em] text-stone-600">
                                Tag
                            </label>
                            <select
                                id="tag"
                                name="tag"
                                class="block w-full rounded-[1.25rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                            >
                                <option value="">Tous les tags</option>
                                @foreach ($tags as $tagOption)
                                    <option value="{{ $tagOption->slug }}" @selected(request('tag') === $tagOption->slug)>
                                        {{ $tagOption->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="search" class="mb-2 block text-sm font-semibold uppercase tracking-[0.16em] text-stone-600">
                                Recherche
                            </label>
                            <input
                                id="search"
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Rechercher un sujet..."
                                class="block w-full rounded-[1.25rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                            >
                        </div>
                        <div>
                            <label for="order" class="mb-2 block text-sm font-semibold uppercase tracking-[0.16em] text-stone-600">
                                Trier par
                            </label>
                            <select
                                id="order"
                                name="order"
                                class="block w-full rounded-[1.25rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                            >
                                <option value="latest" @selected(request('order', 'latest') === 'latest')>Plus recents</option>
                                <option value="popular" @selected(request('order') === 'popular')>Plus actifs</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('topics.index', ['recommended' => 1]) }}" class="rounded-full border border-amber-200 bg-amber-50/90 px-4 py-3 text-sm font-semibold text-amber-700 transition hover:bg-amber-100">
                                Voir les sujets recommandes
                            </a>
                        @endauth
                        @if (request()->filled('search') || request()->filled('order') || request()->filled('recommended'))
                            <a href="{{ route('topics.index') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-white">
                                Reinitialiser
                            </a>
                        @endif
                        <x-primary-button class="justify-center">
                            Filtrer
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <div class="grid gap-6">
                @if ($pinnedTopics->isNotEmpty())
                    <section class="space-y-4">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="section-kicker">Epingle</p>
                                <h3 class="mt-2 text-2xl font-semibold text-stone-950">Sujets a la une</h3>
                            </div>
                            <span class="rounded-full bg-amber-100 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">
                                {{ $pinnedTopics->count() }} sujet(s)
                            </span>
                        </div>
                        @foreach ($pinnedTopics as $topic)
                            <article class="glass-panel rounded-[2rem] border-amber-200/70 bg-[linear-gradient(180deg,rgba(255,251,235,0.96),rgba(255,255,255,0.9))] p-6 shadow-[0_18px_40px_rgba(245,158,11,0.12)] transition duration-200 hover:-translate-y-1 sm:p-7">
                                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                                    <div class="space-y-4">
                                        <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">
                                            <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-700">Epingle</span>
                                            <span class="rounded-full bg-[rgba(79,70,229,0.12)] px-3 py-1 text-[var(--brand)]">Sujet</span>
                                            @auth
                                                @if (in_array($topic->id, $topicsWithUnreadReplies, true))
                                                    <span class="rounded-full bg-rose-100 px-3 py-1 text-rose-600">Nouvelle reponse</span>
                                                @endif
                                            @endauth
                                            @auth
                                                @if (in_array($topic->id, $recommendedTopicIds ?? [], true))
                                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-700">Recommande</span>
                                                @endif
                                            @endauth
                                            @if ($topic->category)
                                                <a href="{{ route('categories.show', $topic->category) }}" class="rounded-full bg-[rgba(20,184,166,0.12)] px-3 py-1 text-[var(--accent)] transition hover:bg-[rgba(20,184,166,0.2)]">
                                                    {{ $topic->category->name }}
                                                </a>
                                            @endif
                                            <span>{{ $topic->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div>
                                            <h3 class="text-3xl font-semibold text-stone-950">
                                                <a href="{{ route('topics.show', $topic) }}" class="transition hover:text-[var(--brand-deep)]">
                                                    {{ $topic->title }}
                                                </a>
                                            </h3>
                                            <div class="mt-3 flex items-center gap-3">
                                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[var(--brand)] text-sm font-semibold uppercase text-white shadow-[0_12px_24px_rgba(79,70,229,0.24)]">
                                                    {{ strtoupper(substr($topic->user->name, 0, 1)) }}
                                                </span>
                                                <div>
                                                    <p class="text-sm text-stone-500">
                                                        Par <span class="font-semibold text-stone-700">{{ $topic->user->name }}</span>
                                                    </p>
                                                    <p class="mt-1 text-xs font-semibold uppercase tracking-[0.16em] text-[var(--brand)]">
                                                        Lvl {{ $topic->user->level }}
                                                    </p>
                                                    <p class="mt-1 text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">
                                                        Rep {{ $topic->user->reputation }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="muted-copy max-w-3xl text-base leading-8">
                                            {{ \Illuminate\Support\Str::limit($topic->content, 180) }}
                                        </p>
                                        @if ($topic->tags->isNotEmpty())
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($topic->tags as $tag)
                                                    <a href="{{ route('tags.show', $tag) }}" class="rounded-full bg-[rgba(79,70,229,0.1)] px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[var(--brand)] transition hover:bg-[rgba(79,70,229,0.16)]">
                                                        {{ $tag->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="shrink-0 space-y-3">
                                        <div class="rounded-[1.5rem] bg-[linear-gradient(135deg,var(--brand),var(--accent-soft))] px-5 py-4 text-center text-white shadow-[0_18px_35px_rgba(79,70,229,0.24)]">
                                            <p class="text-[0.65rem] font-semibold uppercase tracking-[0.24em] text-white/75">Reponses</p>
                                            <p class="mt-2 text-3xl font-semibold">{{ $topic->replies_count }}</p>
                                        </div>
                                        <div class="rounded-full bg-white/80 px-4 py-2 text-center text-xs font-semibold uppercase tracking-[0.18em] text-stone-700">
                                            {{ $topic->favorites_count }} abonnes
                                        </div>
                                        <a href="{{ route('topics.show', $topic) }}" class="block rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-2 text-center text-sm font-semibold text-stone-800 transition hover:bg-white">
                                            Ouvrir
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </section>
                @endif

                @forelse ($topics as $topic)
                    <article class="glass-panel-strong rounded-[2rem] p-6 transition duration-200 hover:-translate-y-1 hover:shadow-[0_26px_60px_rgba(57,72,120,0.16)] sm:p-7">
                        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                            <div class="space-y-4">
                                <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">
                                    @if ($topic->is_pinned)
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-700">Epingle</span>
                                    @endif
                                    <span class="rounded-full bg-[rgba(79,70,229,0.12)] px-3 py-1 text-[var(--brand)]">Sujet</span>
                                    @auth
                                        @if (in_array($topic->id, $topicsWithUnreadReplies, true))
                                            <span class="rounded-full bg-rose-100 px-3 py-1 text-rose-600">Nouvelle reponse</span>
                                        @endif
                                    @endauth
                                    @auth
                                        @if (in_array($topic->id, $recommendedTopicIds ?? [], true))
                                            <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-700">Recommande</span>
                                        @endif
                                    @endauth
                                    @if ($topic->category)
                                        <a href="{{ route('categories.show', $topic->category) }}" class="rounded-full bg-[rgba(20,184,166,0.12)] px-3 py-1 text-[var(--accent)] transition hover:bg-[rgba(20,184,166,0.2)]">
                                            {{ $topic->category->name }}
                                        </a>
                                    @endif
                                    <span>{{ $topic->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-semibold text-stone-950">
                                        <a href="{{ route('topics.show', $topic) }}" class="transition hover:text-[var(--brand-deep)]">
                                            {{ $topic->title }}
                                        </a>
                                    </h3>
                                    <div class="mt-3 flex items-center gap-3">
                                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[var(--brand)] text-sm font-semibold uppercase text-white shadow-[0_12px_24px_rgba(79,70,229,0.24)]">
                                            {{ strtoupper(substr($topic->user->name, 0, 1)) }}
                                        </span>
                                        <div>
                                            <p class="text-sm text-stone-500">
                                                Par <span class="font-semibold text-stone-700">{{ $topic->user->name }}</span>
                                            </p>
                                            <p class="mt-1 text-xs font-semibold uppercase tracking-[0.16em] text-[var(--brand)]">
                                                Lvl {{ $topic->user->level }}
                                            </p>
                                            <p class="mt-1 text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">
                                                Rep {{ $topic->user->reputation }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <p class="muted-copy max-w-3xl text-base leading-8">
                                    {{ \Illuminate\Support\Str::limit($topic->content, 180) }}
                                </p>
                                @if ($topic->tags->isNotEmpty())
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($topic->tags as $tag)
                                            <a href="{{ route('tags.show', $tag) }}" class="rounded-full bg-[rgba(79,70,229,0.1)] px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[var(--brand)] transition hover:bg-[rgba(79,70,229,0.16)]">
                                                {{ $tag->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="shrink-0 space-y-3">
                                <div class="rounded-[1.5rem] bg-[linear-gradient(135deg,var(--brand),var(--accent-soft))] px-5 py-4 text-center text-white shadow-[0_18px_35px_rgba(79,70,229,0.24)]">
                                    <p class="text-[0.65rem] font-semibold uppercase tracking-[0.24em] text-white/75">Reponses</p>
                                    <p class="mt-2 text-3xl font-semibold">{{ $topic->replies_count }}</p>
                                </div>
                                <div class="rounded-full bg-white/80 px-4 py-2 text-center text-xs font-semibold uppercase tracking-[0.18em] text-stone-700">
                                    {{ $topic->favorites_count }} abonnes
                                </div>
                                <div class="rounded-full bg-[rgba(20,184,166,0.12)] px-4 py-2 text-center text-xs font-semibold uppercase tracking-[0.18em] text-[var(--accent)]">
                                    {{ $topic->replies_count > 0 ? 'Actif' : 'Nouveau' }}
                                </div>
                                <a href="{{ route('topics.show', $topic) }}" class="block rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-2 text-center text-sm font-semibold text-stone-800 transition hover:bg-white">
                                    Ouvrir
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="glass-panel rounded-[2.25rem] border-dashed p-12 text-center">
                        <p class="section-kicker">Aucun contenu</p>
                        <h3 class="mt-3 text-3xl font-semibold text-stone-950">Aucun sujet pour le moment</h3>
                        <p class="muted-copy mt-3 text-base">Le premier message peut poser la direction du forum.</p>
                        @auth
                            <a
                                href="{{ route('topics.create') }}"
                                class="mt-6 inline-flex items-center rounded-full bg-[var(--brand)] px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-white shadow-[0_18px_35px_rgba(79,70,229,0.28)] transition hover:-translate-y-0.5 hover:bg-[var(--brand-deep)]"
                            >
                                Creer le premier sujet
                            </a>
                        @endauth
                    </div>
                @endforelse
            </div>

            <div class="glass-panel rounded-[2rem] px-4 py-4 sm:px-6">
                {{ $topics->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
