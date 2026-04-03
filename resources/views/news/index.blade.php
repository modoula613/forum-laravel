<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="section-kicker">Actualites</p>
                <h2 class="mt-3 text-4xl font-semibold text-stone-950">Le fil des actualites</h2>
                <p class="muted-copy mt-3 text-base leading-7">
                    Les actualites recuperees automatiquement, puis classees dans les categories du forum pour rester lisibles et utiles.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="glass-panel rounded-[2rem] p-5 sm:p-6">
                <form method="GET" action="{{ route('news.index') }}" class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="w-full max-w-sm">
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
                                <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-3">
                        @if (request()->filled('category'))
                            <a href="{{ route('news.index') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-white">
                                Reinitialiser
                            </a>
                        @endif
                        <x-primary-button>Filtrer</x-primary-button>
                    </div>
                </form>
            </section>

            <section class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($articles as $article)
                    <article class="glass-panel-strong overflow-hidden rounded-[2rem] p-6 transition hover:-translate-y-0.5 hover:shadow-[0_18px_40px_rgba(15,23,42,0.08)]">
                        @if ($article->image_url)
                            <div class="mb-5 overflow-hidden rounded-[1.5rem] bg-stone-100">
                                <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="block h-auto w-full">
                            </div>
                        @endif
                        <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">
                            @if ($article->category)
                                <a href="{{ route('categories.show', $article->category) }}" class="rounded-full bg-[rgba(154,90,46,0.12)] px-3 py-1 text-[var(--accent)] transition hover:bg-[rgba(154,90,46,0.18)]">
                                    {{ $article->category->name }}
                                </a>
                            @endif
                            @if ($article->source_name)
                                <span>{{ $article->source_name }}</span>
                            @endif
                            @if ($article->published_at)
                                <span>{{ $article->published_at->format('d/m/Y H:i') }}</span>
                            @endif
                        </div>
                        <h3 class="mt-4 text-2xl font-semibold text-stone-950">{{ $article->title }}</h3>
                        @if ($article->excerpt)
                            <p class="muted-copy mt-3 text-sm leading-7">{{ $article->excerpt }}</p>
                        @endif
                        <div class="mt-5 flex items-center justify-between gap-3">
                            <a href="{{ $article->source_url }}" target="_blank" rel="noopener noreferrer" class="rounded-full bg-[var(--brand)] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[var(--brand-deep)]">
                                Lire l'article
                            </a>
                            @if ($article->category)
                                <a href="{{ route('categories.show', $article->category) }}" class="text-sm font-semibold text-stone-700 transition hover:text-[var(--brand-deep)]">
                                    Voir la categorie
                                </a>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="glass-panel rounded-[2rem] border-dashed p-12 text-center md:col-span-2 xl:col-span-3">
                        <p class="section-kicker">Aucune actualite</p>
                        <h3 class="mt-3 text-3xl font-semibold text-stone-950">Le fil d’actualites est vide pour le moment</h3>
                        <p class="muted-copy mt-3 text-base">
                            Lance la synchronisation avec <code class="rounded bg-white/70 px-2 py-1 text-sm">php artisan news:sync</code> apres avoir renseigne ta cle GNews.
                        </p>
                    </div>
                @endforelse
            </section>

            <div class="glass-panel rounded-[2rem] px-4 py-4 sm:px-6">
                {{ $articles->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
