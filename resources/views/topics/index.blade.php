<x-app-layout>
    <x-slot name="header">
        <div class="space-y-5">
            <div class="x-feed-tabs">
                <a href="{{ route('topics.index') }}" class="x-feed-tab {{ $followingOnly ? '' : 'is-active' }}">
                    Pour toi
                </a>
                @auth
                    <a href="{{ route('topics.index', ['following' => 1]) }}" class="x-feed-tab {{ $followingOnly ? 'is-active' : '' }}">
                        Abonnements
                    </a>
                @else
                    <span class="x-feed-tab opacity-50">
                        Abonnements
                    </span>
                @endauth
            </div>

            <div
                x-data="forumSearch({
                    initialQuery: @js((string) request('search')),
                    action: @js(route('topics.index')),
                    suggestionsUrl: @js(route('search.suggestions')),
                })"
                @click.outside="close()"
                class="space-y-3"
            >
                <form x-ref="form" method="GET" action="{{ route('topics.index') }}" class="flex flex-col gap-3">
                    @if (request()->filled('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if (request()->filled('tag'))
                        <input type="hidden" name="tag" value="{{ request('tag') }}">
                    @endif
                    @if (request()->filled('order'))
                        <input type="hidden" name="order" value="{{ request('order') }}">
                    @endif
                    @if ($followingOnly)
                        <input type="hidden" name="following" value="1">
                    @endif

                    <div class="min-w-0 flex-1">
                        <label for="search-bar" class="sr-only">
                            Rechercher un sujet
                        </label>
                        <div class="x-search-shell" :class="{ 'is-open': open }">
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
                                    role="combobox"
                                    aria-autocomplete="list"
                                    :aria-expanded="open"
                                    :aria-controls="listboxId"
                                    :aria-activedescendant="activeDescendant"
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
                                :id="listboxId"
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
                                                        :id="optionId(flatItems.findIndex((entry) => entry === item))"
                                                        class="x-search-item"
                                                        :class="{ 'is-active': activeIndex === flatItems.findIndex((entry) => entry === item) }"
                                                        role="option"
                                                        :aria-selected="activeIndex === flatItems.findIndex((entry) => entry === item)"
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

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-2 text-[0.72rem] font-semibold uppercase tracking-[0.18em] text-white/40">
                            <span>Recherche rapide</span>
                            <span class="rounded-full border border-white/10 px-3 py-1 text-white/62">user:nom</span>
                            <span class="rounded-full border border-white/10 px-3 py-1 text-white/62">#hashtag</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <button
                                type="button"
                                x-on:click="$dispatch('toggle-filters')"
                                class="rounded-full border border-white/10 px-4 py-3 text-sm font-semibold text-white/72 transition hover:bg-white/8 hover:text-white"
                            >
                                Filtres
                            </button>
                            @if (request()->filled('search') || request()->filled('category') || request()->filled('tag') || request()->filled('order') || $followingOnly)
                                <a href="{{ route('topics.index') }}" class="rounded-full border border-white/10 px-4 py-3 text-sm font-semibold text-white/72 transition hover:bg-white/8 hover:text-white">
                                    Reinitialiser
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-[1080px] space-y-8 px-0 sm:px-0 lg:px-0">
            @if ($announcements->isNotEmpty())
                <section class="space-y-3">
                    @foreach ($announcements as $announcement)
                        <div class="glass-panel border-[var(--brand)]/20 bg-[var(--brand)]/8 px-5 py-4 text-sm">
                            <p class="section-kicker">Annonce</p>
                            <p class="mt-2 text-lg font-semibold text-white">{{ $announcement->title }}</p>
                            <p class="mt-2 max-w-4xl text-white/70">{{ $announcement->content }}</p>
                        </div>
                    @endforeach
                </section>
            @endif

            @if (session('success'))
                <div class="glass-panel border-emerald-500/20 bg-emerald-500/10 px-5 py-4 text-sm text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="glass-panel border-rose-500/20 bg-rose-500/10 px-5 py-4 text-sm text-rose-200">
                    {{ session('error') }}
                </div>
            @endif

            @auth
                @if (auth()->user()->is_blocked)
                    <div class="glass-panel border-rose-500/20 bg-rose-500/10 p-6 text-sm font-medium text-rose-200">
                        Votre compte est bloque suite a plusieurs infractions.
                    </div>
                @endif
                @if ($followingOnly)
                    <div class="glass-panel border-amber-500/20 bg-amber-500/10 p-6 text-sm font-medium text-amber-200">
                        Affichage des sujets publies par les membres que vous suivez.
                    </div>
                @endif
                @if ($followingOnly && $followedUserIds->isEmpty())
                    <div class="glass-panel border-white/10 bg-white/5 p-6 text-sm font-medium text-white/80">
                        Tu ne suis encore personne. Ouvre un profil membre pour commencer a suivre des personnes et voir ici uniquement leurs sujets.
                    </div>
                @endif
            @endauth

            @guest
                <div class="glass-panel p-6">
                    <p class="text-sm text-white/70">
                        Connecte-toi pour creer un sujet ou repondre.
                        <a href="{{ route('login') }}" class="font-semibold text-white underline underline-offset-4">Se connecter</a>
                    </p>
                </div>
            @endguest

            @php
                $activeFilters = array_filter([
                    request('search') ? 'Recherche : '.request('search') : null,
                    request('category') ? 'Categorie active' : null,
                    request('tag') ? 'Tag : '.request('tag') : null,
                    request('order') === 'popular' ? 'Tri : plus actifs' : null,
                    $followingOnly ? 'Suivi des membres' : null,
                ]);
                $hasAdvancedFilters = request()->filled('category') || request()->filled('tag') || request()->filled('order');
            @endphp

            <div
                x-data="{ filtersOpen: @js($hasAdvancedFilters) }"
                x-on:toggle-filters.window="filtersOpen = ! filtersOpen"
                class="space-y-4"
            >
                @if ($activeFilters)
                    <div class="glass-panel-strong p-5">
                        <div class="flex flex-wrap gap-2">
                            @foreach ($activeFilters as $filter)
                                <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.16em] text-white/70">
                                    {{ $filter }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div x-cloak x-show="filtersOpen" x-transition.opacity.duration.200ms class="glass-panel x-shell-divider p-5 sm:p-6">
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div>
                            <p class="section-kicker">Filtres</p>
                            <h3 class="mt-2 text-xl font-semibold text-white">Affiner le flux</h3>
                        </div>
                        <button type="button" @click="filtersOpen = false" class="rounded-full border border-white/10 px-3 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-white/70 transition hover:bg-white/8 hover:text-white">
                            Fermer
                        </button>
                    </div>

                    <form method="GET" action="{{ route('topics.index') }}" class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        @if ($followingOnly)
                            <input type="hidden" name="following" value="1">
                        @endif

                        <div class="grid gap-4 sm:grid-cols-2 lg:w-full xl:grid-cols-3">
                        <div>
                            <label for="category" class="mb-2 block text-sm font-semibold uppercase tracking-[0.16em] text-stone-600">
                                Categorie
                            </label>
                            <select
                                id="category"
                                name="category"
                                class="block w-full rounded-[1.25rem] border border-white/10 bg-white/5 px-4 py-3 text-white shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
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
                                class="block w-full rounded-[1.25rem] border border-white/10 bg-white/5 px-4 py-3 text-white shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
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
                                class="block w-full rounded-[1.25rem] border border-white/10 bg-white/5 px-4 py-3 text-white shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                            >
                                <option value="latest" @selected(request('order', 'latest') === 'latest')>Plus recents</option>
                                <option value="popular" @selected(request('order') === 'popular')>Plus actifs</option>
                            </select>
                        </div>
                    </div>
                        <div class="flex items-center gap-3">
                            @auth
                                <a href="{{ route('topics.index', ['following' => 1]) }}" class="rounded-full border border-white/10 px-4 py-3 text-sm font-semibold text-white/72 transition hover:bg-white/8 hover:text-white">
                                    Voir mes suivis
                                </a>
                            @endauth
                            <x-primary-button class="justify-center">
                                Filtrer
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_21rem]">
                <div class="space-y-6">
                    <section class="glass-panel x-shell-divider p-5 sm:p-6">
                        <div class="flex items-start gap-4">
                            <span class="mt-1 flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white/8 text-sm font-semibold uppercase text-white/80">
                                @auth
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                @else
                                    ?
                                @endauth
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-2xl font-medium text-white/45">
                                    Que se passe-t-il ?
                                </p>
                                <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-white/10 pt-4">
                                    <div class="flex flex-wrap gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-[var(--brand)]">
                                        <span class="rounded-full bg-[var(--brand)]/10 px-3 py-1">Debat</span>
                                        <span class="rounded-full bg-[var(--brand)]/10 px-3 py-1">Question</span>
                                        <span class="rounded-full bg-[var(--brand)]/10 px-3 py-1">Reaction</span>
                                    </div>
                                    @auth
                                        @if (! auth()->user()->is_blocked)
                                            <a href="{{ route('topics.create') }}" class="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-black transition hover:bg-white/90">
                                                Poster
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-black transition hover:bg-white/90">
                                            Se connecter
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </section>

                    @if ($pinnedTopics->isNotEmpty())
                        <section class="space-y-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="px-1 pt-1">
                                    <p class="section-kicker">Mis en avant</p>
                                    <h3 class="mt-2 text-2xl font-semibold text-white">Sujets a la une</h3>
                                </div>
                            </div>
                            @foreach ($pinnedTopics as $topic)
                                <article class="glass-panel x-shell-divider rounded-[1.9rem] p-5 sm:px-6">
                                    <div class="flex gap-4">
                                        <span class="mt-1 flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-amber-500/10 text-sm font-semibold uppercase text-amber-300">
                                            {{ strtoupper(substr($topic->user->name, 0, 1)) }}
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-white/45">
                                                <span class="rounded-full bg-amber-500/10 px-3 py-1 text-amber-300">Epingle</span>
                                                <span>{{ $topic->user->name }}</span>
                                                <span>{{ $topic->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <h4 class="mt-3 text-xl font-semibold text-white">
                                                <a href="{{ route('topics.show', $topic) }}" class="transition hover:text-[var(--brand)]">
                                                    {{ $topic->title }}
                                                </a>
                                            </h4>
                                            <p class="mt-2 text-sm leading-7 text-white/70">
                                                {{ \Illuminate\Support\Str::limit($topic->content, 120) }}
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </section>
                    @endif

                    <section class="space-y-4">
                        <div class="flex items-center justify-between gap-3 px-1 pt-1">
                            <div>
                                <p class="section-kicker">Flux</p>
                                <h3 class="mt-2 text-2xl font-semibold text-white">Ce que les membres racontent</h3>
                                <p class="mt-2 text-sm text-white/48">
                                    Des sujets simples, des prises de position, des questions directes. Le forum vit d'abord par les messages des membres.
                                </p>
                            </div>
                            @auth
                                <a href="{{ route('topics.create') }}" class="hidden rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-white/72 transition hover:bg-white/8 hover:text-white sm:inline-flex">
                                    Ecrire
                                </a>
                            @endauth
                        </div>

                        @forelse ($topics as $topic)
                            <article class="glass-panel-strong x-shell-divider rounded-[1.95rem] p-5 transition duration-200 hover:bg-white/[0.02] sm:px-6">
                                <div class="flex gap-4">
                                    <span class="mt-1 flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white/8 text-sm font-semibold uppercase text-white/85">
                                        {{ strtoupper(substr($topic->user->name, 0, 1)) }}
                                    </span>
                                    <div class="min-w-0 flex-1 space-y-3">
                                        <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-white/45">
                                            <span class="text-white">{{ $topic->user->name }}</span>
                                            @if ($topic->category)
                                                <a href="{{ route('categories.show', $topic->category) }}" class="rounded-full bg-white/6 px-3 py-1 text-white/65 transition hover:bg-white/10">
                                                    {{ $topic->category->name }}
                                                </a>
                                            @endif
                                            @auth
                                                @if (in_array($topic->id, $topicsWithUnreadReplies, true))
                                                    <span class="rounded-full bg-rose-500/10 px-3 py-1 text-rose-300">Nouvelle reponse</span>
                                                @endif
                                            @endauth
                                            @auth
                                                @if (in_array($topic->user_id, $followedAuthorIds ?? [], true))
                                                    <span class="rounded-full bg-amber-500/10 px-3 py-1 text-amber-300">Suivi</span>
                                                @endif
                                            @endauth
                                            <span>{{ $topic->created_at->diffForHumans() }}</span>
                                        </div>
                                        <h4 class="text-[1.4rem] font-semibold leading-tight text-white">
                                            <a href="{{ route('topics.show', $topic) }}" class="transition hover:text-[var(--brand)]">
                                                {{ $topic->title }}
                                            </a>
                                        </h4>
                                        <p class="max-w-3xl text-[0.98rem] leading-7 text-white/72">
                                            {{ \Illuminate\Support\Str::limit($topic->content, 180) }}
                                        </p>
                                        <div class="flex flex-wrap items-center gap-4 text-sm text-white/45">
                                            <span>{{ $topic->replies_count }} reponse(s)</span>
                                            @if ($topic->favorites_count > 0)
                                                <span>{{ $topic->favorites_count }} favori(s)</span>
                                            @endif
                                            <a href="{{ route('topics.show', $topic) }}" class="font-semibold text-[var(--brand)] transition hover:text-white">
                                                Voir la discussion
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="glass-panel x-shell-divider rounded-[1.95rem] border-dashed p-12 text-center">
                                <p class="section-kicker">Aucun contenu</p>
                                <h3 class="mt-3 text-3xl font-semibold text-white">Le flux attend ses premiers messages</h3>
                                <p class="mt-3 text-base text-white/60">L'actualite peut donner des idees, mais ce sont surtout les sujets des membres qui feront vivre le forum.</p>
                                @auth
                                    <a
                                        href="{{ route('topics.create') }}"
                                        class="mt-6 inline-flex items-center rounded-full bg-white px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-black transition hover:bg-white/90"
                                    >
                                        Creer le premier sujet
                                    </a>
                                @endauth
                            </div>
                        @endforelse
                    </section>
                </div>

                <aside class="space-y-4">
                    <section class="glass-panel rounded-[1.8rem] p-5">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="section-kicker">A surveiller</p>
                                <h3 class="mt-2 text-xl font-semibold text-white">Ce qui bouge</h3>
                            </div>
                        </div>
                        <div class="mt-5 space-y-4 text-sm">
                            @foreach ($categories->take(4) as $category)
                                <a href="{{ route('topics.index', ['category' => $category->id]) }}" class="block rounded-[1.2rem] bg-white/4 px-4 py-4 transition hover:bg-white/8">
                                    <p class="font-semibold text-white">{{ $category->name }}</p>
                                    <p class="mt-1 text-white/42">Voir les discussions de cette categorie</p>
                                </a>
                            @endforeach
                        </div>
                    </section>

                    @if ($forumNews->isNotEmpty())
                        <section class="glass-panel rounded-[1.8rem] p-5">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="section-kicker">Actualites reliees</p>
                                    <h3 class="mt-2 text-xl font-semibold text-white">What's happening</h3>
                                </div>
                                <a href="{{ route('news.index', array_filter(['category' => request('category') ? $categories->firstWhere('id', request('category'))?->slug : null])) }}" class="text-sm font-semibold text-white/70 transition hover:text-white">
                                    Voir le fil
                                </a>
                            </div>
                            <p class="mt-3 text-sm leading-6 text-white/48">
                                L'actualite reste secondaire. Elle sert surtout a lancer des reactions et des discussions.
                            </p>

                            <div class="mt-5 space-y-3">
                                @foreach ($forumNews as $article)
                                    <a
                                        href="{{ $article->source_url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="group block rounded-[1.35rem] border border-white/8 bg-white/[0.03] p-4 transition duration-200 hover:bg-white/[0.05]"
                                    >
                                        @if ($article->image_url)
                                            <div class="overflow-hidden rounded-[1rem] border border-white/8 bg-black">
                                                <img
                                                    src="{{ $article->image_url }}"
                                                    alt="{{ $article->title }}"
                                                    class="block h-auto w-full"
                                                >
                                            </div>
                                        @endif
                                        <div class="mt-3 flex flex-wrap items-center gap-2 text-[0.68rem] font-semibold uppercase tracking-[0.18em] text-white/42">
                                            @if ($article->category)
                                                <span class="rounded-full bg-white/8 px-2.5 py-1 text-white/68">{{ $article->category->name }}</span>
                                            @endif
                                            @if ($article->source_name)
                                                <span>{{ $article->source_name }}</span>
                                            @endif
                                        </div>
                                        <p class="text-clamp-3 mt-3 text-sm font-semibold leading-6 text-white transition group-hover:text-[var(--brand)]">
                                            {{ $article->title }}
                                        </p>
                                        @if ($article->excerpt)
                                            <p class="text-clamp-3 mt-2 text-sm leading-6 text-white/52">
                                                {{ $article->excerpt }}
                                            </p>
                                        @endif
                                        @if ($article->category)
                                            <div class="mt-3">
                                                <span class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--brand)]">
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

            <div class="glass-panel px-4 py-4 sm:px-6">
                {{ $topics->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
