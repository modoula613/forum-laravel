<x-app-layout :meta-description="$category->description ?: 'Tous les sujets rattaches a cette categorie sur Sphere.'">
    <x-slot name="header">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="section-kicker">Categorie</p>
                <h2 class="mt-3 text-4xl font-semibold text-stone-950">{{ $category->name }}</h2>
                <p class="muted-copy mt-3 text-base leading-7">
                    {{ $category->description ?: 'Un espace de discussion pour retrouver les sujets lies a cette categorie, du plus recent au plus actif.' }}
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <div class="rounded-full border border-[rgba(71,85,135,0.14)] bg-white/70 px-4 py-2 text-sm font-medium text-stone-600">
                    {{ $category->topics_count }} sujet(s)
                </div>
                @auth
                    @if (! auth()->user()->is_blocked)
                        <a href="{{ route('topics.create') }}" class="rounded-full bg-[var(--brand)] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[var(--brand-deep)]">
                            Creer un sujet ici
                        </a>
                    @endif
                @endauth
                <a href="{{ route('categories.index') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                    Toutes les categories
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-8 px-4 sm:px-6 lg:px-8">
            @if ($categoryNews->isNotEmpty())
                <section class="glass-panel rounded-[2rem] p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="section-kicker">Actualites liees</p>
                            <h3 class="mt-2 text-2xl font-semibold text-stone-950">De quoi lancer des discussions</h3>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-stone-500">
                                Ici, l'actualite reste un point de depart. Le plus important, ce sont les sujets que la communaute ouvre autour.
                            </p>
                        </div>
                        <a href="{{ route('news.index', ['category' => $category->slug]) }}" class="text-sm font-semibold text-stone-800 transition hover:text-[var(--brand-deep)]">
                            Voir le fil
                        </a>
                    </div>
                    <div class="mt-5 grid gap-3 lg:grid-cols-3">
                        @foreach ($categoryNews as $article)
                            <a
                                href="{{ $article->source_url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="rounded-[1.35rem] bg-white/72 p-4 transition duration-200 hover:-translate-y-0.5 hover:bg-white"
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
                                    @if ($article->source_name)
                                        <span>{{ $article->source_name }}</span>
                                    @endif
                                    @if ($article->published_at)
                                        <span>{{ $article->published_at->format('d/m/Y H:i') }}</span>
                                    @endif
                                </div>
                                <p class="text-clamp-3 mt-3 text-base font-semibold leading-6 text-stone-900">
                                    {{ $article->title }}
                                </p>
                                @if ($article->excerpt)
                                    <p class="text-clamp-3 mt-2 text-sm leading-6 text-stone-600">
                                        {{ $article->excerpt }}
                                    </p>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="glass-panel rounded-[2.2rem] p-6 sm:p-7">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="section-kicker">Discussions</p>
                        <h3 class="mt-2 text-3xl font-semibold text-stone-950">Les sujets de {{ $category->name }}</h3>
                    </div>
                    @if ($relatedCategories->isNotEmpty())
                        <div class="flex flex-wrap gap-2">
                            @foreach ($relatedCategories as $relatedCategory)
                                <a href="{{ route('categories.show', $relatedCategory) }}" class="rounded-full bg-white/80 px-4 py-2 text-sm font-semibold text-stone-800 transition hover:bg-white">
                                    {{ $relatedCategory->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="mt-6 grid gap-4">
                    @forelse ($topics as $topic)
                        <article class="rounded-[1.75rem] border border-[rgba(71,85,135,0.1)] bg-white/74 p-5 transition duration-200 hover:-translate-y-0.5 hover:bg-white">
                            <div class="flex gap-4">
                                <x-user-avatar :user="$topic->user" class="mt-1 h-11 w-11 shrink-0 bg-[rgba(139,92,246,0.12)] text-sm font-semibold uppercase text-[var(--brand)]" />
                                <div class="min-w-0 flex-1 space-y-3">
                                    <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">
                                        <span>{{ $topic->created_at->format('d/m/Y H:i') }}</span>
                                        <span class="rounded-full bg-[rgba(20,184,166,0.12)] px-3 py-1 text-[var(--accent)]">{{ $category->name }}</span>
                                    </div>
                                    <div class="text-sm text-stone-500">
                                        <x-user-link :user="$topic->user" class="font-semibold text-stone-900">
                                            {{ $topic->user->name }}
                                        </x-user-link>
                                    </div>
                                    <h3 class="text-2xl font-semibold text-stone-950">
                                        <a href="{{ route('topics.show', $topic) }}" class="transition hover:text-[var(--brand-deep)]">
                                            {{ $topic->title }}
                                        </a>
                                    </h3>
                                    <p class="muted-copy max-w-3xl text-base leading-8">
                                        {{ \Illuminate\Support\Str::limit($topic->content, 200) }}
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
                                    <div class="flex flex-wrap items-center gap-4 text-sm text-stone-500">
                                        <span>{{ $topic->replies_count }} reponse(s)</span>
                                        <a href="{{ route('topics.show', $topic) }}" class="font-semibold text-[var(--brand-deep)] transition hover:text-[var(--brand)]">
                                            Ouvrir
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-[1.85rem] border border-dashed border-[rgba(71,85,135,0.16)] bg-white/60 p-12 text-center">
                            <p class="section-kicker">Aucun sujet</p>
                            <h3 class="mt-3 text-3xl font-semibold text-stone-950">Cette categorie est encore vide</h3>
                            <p class="muted-copy mt-3 text-base">Elle commencera a vivre des qu'un premier sujet sera publie ici.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <div class="glass-panel rounded-[2rem] px-4 py-4 sm:px-6">
                {{ $topics->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
