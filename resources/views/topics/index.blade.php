<x-app-layout>
    <x-slot name="header">
        <div class="space-y-6">
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

            <div
                x-data="forumSearch({
                    initialQuery: @js((string) request('search')),
                    action: @js(route('topics.index')),
                    suggestionsUrl: @js(route('search.suggestions')),
                })"
                @click.outside="close()"
                class="space-y-4"
            >
                <form x-ref="form" method="GET" action="{{ route('topics.index') }}" class="flex flex-col gap-4">
                    @if (request()->filled('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if (request()->filled('tag'))
                        <input type="hidden" name="tag" value="{{ request('tag') }}">
                    @endif
                    @if (request()->filled('order'))
                        <input type="hidden" name="order" value="{{ request('order') }}">
                    @endif
                    @if (request()->filled('recommended'))
                        <input type="hidden" name="recommended" value="{{ request('recommended') }}">
                    @endif

                    <div class="min-w-0 flex-1">
                        <label for="search-bar" class="mb-2 block text-sm font-semibold uppercase tracking-[0.16em] text-stone-600">
                            Rechercher un sujet
                        </label>
                        <div class="x-search-shell">
                            <div class="x-search-field">
                                <svg class="x-search-icon" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill="currentColor" d="M10.5 4a6.5 6.5 0 1 0 4.03 11.6l4.44 4.43 1.06-1.06-4.43-4.44A6.5 6.5 0 0 0 10.5 4Zm0 1.5a5 5 0 1 1 0 10 5 5 0 0 1 0-10Z"/>
                                </svg>
                                <input
                                    x-ref="input"
                                    x-model="query"
                                    @input="onInput()"
                                    @focus="onFocus()"
                                    @keydown="onKeydown($event)"
                                    id="search-bar"
                                    type="text"
                                    name="search"
                                    placeholder="Rechercher..."
                                    aria-label="Rechercher"
                                    aria-autocomplete="list"
                                    :aria-expanded="open"
                                    aria-controls="forum-search-dropdown"
                                    autocomplete="off"
                                    class="x-search-input"
                                >
                                <button
                                    x-cloak
                                    x-show="query.length > 0"
                                    type="button"
                                    @click="clear()"
                                    class="x-search-clear"
                                    aria-label="Effacer la recherche"
                                >
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill="currentColor" d="M6.7 5.64 12 10.94l5.3-5.3 1.06 1.06-5.3 5.3 5.3 5.3-1.06 1.06-5.3-5.3-5.3 5.3-1.06-1.06 5.3-5.3-5.3-5.3 1.06-1.06Z"/>
                                    </svg>
                                </button>
                            </div>

                            <div
                                x-cloak
                                x-show="open"
                                x-transition.opacity.duration.200ms
                                id="forum-search-dropdown"
                                class="x-search-dropdown"
                                role="listbox"
                            >
                                <template x-if="loading">
                                    <div class="x-search-status">Recherche en cours...</div>
                                </template>

                                <template x-if="!loading && visibleSections.length === 0">
                                    <div class="x-search-status">Aucune suggestion pour le moment.</div>
                                </template>

                                <template x-for="(section, sectionIndex) in visibleSections" :key="section.label + sectionIndex">
                                    <div class="x-search-section">
                                        <p class="x-search-section-title" x-text="section.label"></p>
                                        <div class="x-search-list">
                                            <template x-for="(item, itemIndex) in section.items" :key="`${section.label}-${item.type}-${item.title}-${itemIndex}`">
                                                <button
                                                    type="button"
                                                    class="x-search-item"
                                                    :class="{ 'is-active': activeIndex === flatItems.findIndex((entry) => entry === item) }"
                                                    @mouseenter="activeIndex = flatItems.findIndex((entry) => entry === item)"
                                                    @click="selectItem(item)"
                                                >
                                                    <span class="x-search-item-title" x-text="item.title"></span>
                                                    <span class="x-search-item-subtitle" x-text="item.subtitle || ''"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <x-primary-button type="button" @click="submitSearch()" class="justify-center rounded-full px-5 py-4 text-sm">
                            Rechercher
                        </x-primary-button>
                        @if (request()->filled('search') || request()->filled('category') || request()->filled('tag') || request()->filled('order') || request()->filled('recommended'))
                            <a href="{{ route('topics.index') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-white">
                                Reinitialiser
                            </a>
                        @endif
                    </div>
                </form>
            </div>
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

            @php
                $activeFilters = array_filter([
                    request('search') ? 'Recherche : '.request('search') : null,
                    request('category') ? 'Categorie active' : null,
                    request('tag') ? 'Tag : '.request('tag') : null,
                    request('order') === 'popular' ? 'Tri : plus actifs' : null,
                    request('recommended') ? 'Suggestions personnalisees' : null,
                ]);
            @endphp

            <div class="space-y-4">
                @if ($activeFilters)
                    <div class="glass-panel-strong rounded-[1.9rem] p-5 sm:p-6">
                        <div class="flex flex-wrap gap-2">
                            @foreach ($activeFilters as $filter)
                                <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.16em] text-stone-600">
                                    {{ $filter }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="glass-panel rounded-[1.75rem] p-5 sm:p-6">
                    <div class="mb-4">
                        <p class="section-kicker">Filtres</p>
                        <h3 class="mt-2 text-xl font-semibold text-stone-950">Affiner le flux</h3>
                    </div>

                    <form method="GET" action="{{ route('topics.index') }}" class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <input type="hidden" name="search" value="{{ request('search') }}">

                        <div class="grid gap-4 sm:grid-cols-2 lg:w-full xl:grid-cols-3">
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
                        <x-primary-button class="justify-center">
                            Filtrer
                        </x-primary-button>
                    </div>
                </form>
            </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
                <div class="space-y-4">
                    @if ($pinnedTopics->isNotEmpty())
                        <section class="space-y-3">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="section-kicker">Mis en avant</p>
                                    <h3 class="mt-2 text-2xl font-semibold text-stone-950">Sujets a la une</h3>
                                </div>
                            </div>
                            @foreach ($pinnedTopics as $topic)
                                <article class="glass-panel rounded-[1.6rem] border-amber-200/70 bg-[linear-gradient(180deg,rgba(255,251,235,0.96),rgba(255,255,255,0.94))] p-5">
                                    <div class="flex gap-4">
                                        <span class="mt-1 flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-amber-100 text-sm font-semibold uppercase text-amber-700">
                                            {{ strtoupper(substr($topic->user->name, 0, 1)) }}
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">
                                                <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-700">Epingle</span>
                                                <span>{{ $topic->user->name }}</span>
                                                <span>{{ $topic->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <h4 class="mt-3 text-xl font-semibold text-stone-950">
                                                <a href="{{ route('topics.show', $topic) }}" class="transition hover:text-[var(--brand-deep)]">
                                                    {{ $topic->title }}
                                                </a>
                                            </h4>
                                            <p class="mt-2 text-sm leading-7 text-stone-600">
                                                {{ \Illuminate\Support\Str::limit($topic->content, 120) }}
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </section>
                    @endif

                    <section class="space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="section-kicker">Flux</p>
                                <h3 class="mt-2 text-2xl font-semibold text-stone-950">Ce que les membres racontent</h3>
                                <p class="mt-2 text-sm text-stone-500">
                                    Des sujets simples, des prises de position, des questions directes. Le forum vit d'abord par les messages des membres.
                                </p>
                            </div>
                            @auth
                                <a href="{{ route('topics.create') }}" class="hidden rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-2 text-sm font-semibold text-stone-800 transition hover:bg-white sm:inline-flex">
                                    Ecrire
                                </a>
                            @endauth
                        </div>

                        @forelse ($topics as $topic)
                            <article class="glass-panel-strong rounded-[1.85rem] p-6 transition duration-200 hover:-translate-y-0.5 sm:p-7">
                                <div class="flex gap-4">
                                    <span class="mt-1 flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[rgba(29,155,240,0.12)] text-sm font-semibold uppercase text-[var(--brand)]">
                                        {{ strtoupper(substr($topic->user->name, 0, 1)) }}
                                    </span>
                                    <div class="min-w-0 flex-1 space-y-3">
                                        <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">
                                            <span class="text-stone-900">{{ $topic->user->name }}</span>
                                            @if ($topic->category)
                                                <a href="{{ route('categories.show', $topic->category) }}" class="rounded-full bg-slate-100 px-3 py-1 text-stone-600 transition hover:bg-slate-200">
                                                    {{ $topic->category->name }}
                                                </a>
                                            @endif
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
                                            <span>{{ $topic->created_at->diffForHumans() }}</span>
                                        </div>
                                        <h4 class="text-[1.55rem] font-semibold leading-tight text-stone-950">
                                            <a href="{{ route('topics.show', $topic) }}" class="transition hover:text-[var(--brand-deep)]">
                                                {{ $topic->title }}
                                            </a>
                                        </h4>
                                        <p class="muted-copy max-w-3xl text-base leading-8">
                                            {{ \Illuminate\Support\Str::limit($topic->content, 180) }}
                                        </p>
                                        <div class="flex flex-wrap items-center gap-4 text-sm text-stone-500">
                                            <span>{{ $topic->replies_count }} reponse(s)</span>
                                            @if ($topic->favorites_count > 0)
                                                <span>{{ $topic->favorites_count }} favori(s)</span>
                                            @endif
                                            <a href="{{ route('topics.show', $topic) }}" class="font-semibold text-[var(--brand-deep)] transition hover:text-[var(--brand)]">
                                                Voir la discussion
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="glass-panel rounded-[2.25rem] border-dashed p-12 text-center">
                                <p class="section-kicker">Aucun contenu</p>
                                <h3 class="mt-3 text-3xl font-semibold text-stone-950">Le flux attend ses premiers messages</h3>
                                <p class="muted-copy mt-3 text-base">L'actualite peut donner des idees, mais ce sont surtout les sujets des membres qui feront vivre le forum.</p>
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
                    </section>
                </div>

                <aside class="space-y-4">
                    @if ($forumNews->isNotEmpty())
                        <section class="glass-panel rounded-[1.8rem] p-5">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="section-kicker">Actualites reliees</p>
                                    <h3 class="mt-2 text-xl font-semibold text-stone-950">A commenter</h3>
                                </div>
                                <a href="{{ route('news.index', array_filter(['category' => request('category') ? $categories->firstWhere('id', request('category'))?->slug : null])) }}" class="text-sm font-semibold text-stone-800 transition hover:text-[var(--brand-deep)]">
                                    Voir tout
                                </a>
                            </div>
                            <p class="mt-3 text-sm leading-6 text-stone-600">
                                Les actualites restent un point de depart. Le vrai coeur du forum, c'est la discussion qui nait derriere.
                            </p>

                            <div class="mt-5 space-y-3">
                                @foreach ($forumNews as $article)
                                    <a
                                        href="{{ $article->source_url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="group block rounded-[1.35rem] border border-[rgba(34,92,143,0.08)] bg-white/82 p-4 transition duration-200 hover:-translate-y-0.5 hover:bg-white"
                                    >
                                        @if ($article->image_url)
                                            <div class="overflow-hidden rounded-[1rem] border border-slate-100 bg-slate-50">
                                                <img
                                                    src="{{ $article->image_url }}"
                                                    alt="{{ $article->title }}"
                                                    class="block h-auto w-full"
                                                >
                                            </div>
                                        @endif
                                        <div class="mt-3 flex flex-wrap items-center gap-2 text-[0.68rem] font-semibold uppercase tracking-[0.18em] text-stone-500">
                                            @if ($article->category)
                                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-stone-700">{{ $article->category->name }}</span>
                                            @endif
                                            @if ($article->source_name)
                                                <span>{{ $article->source_name }}</span>
                                            @endif
                                        </div>
                                        <p class="mt-3 text-sm font-semibold leading-6 text-stone-900 transition group-hover:text-[var(--brand-deep)]">
                                            {{ \Illuminate\Support\Str::limit($article->title, 105) }}
                                        </p>
                                        @if ($article->excerpt)
                                            <p class="mt-2 text-sm leading-6 text-stone-600">
                                                {{ \Illuminate\Support\Str::limit($article->excerpt, 95) }}
                                            </p>
                                        @endif
                                        @if ($article->category)
                                            <div class="mt-3">
                                                <span class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--brand-deep)]">
                                                    Ouvrir la discussion dans {{ $article->category->name }}
                                                </span>
                                            </div>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </aside>
            </div>

            <div class="glass-panel rounded-[2rem] px-4 py-4 sm:px-6">
                {{ $topics->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
