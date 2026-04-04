<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Categories</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Explorer les categories</h2>
            <p class="muted-copy mt-3 text-base leading-7">
                Retrouve les espaces de discussion les plus actifs et entre directement dans le bon univers.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            @forelse ($categories as $category)
                <article class="glass-panel-strong rounded-[2rem] p-6 transition duration-200 hover:-translate-y-1 hover:shadow-[0_26px_60px_rgba(57,72,120,0.16)]">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="section-kicker">Categorie</p>
                            <h3 class="mt-2 text-3xl font-semibold text-stone-950">{{ $category->name }}</h3>
                            <p class="mt-3 text-sm text-stone-500">{{ $category->topics_count }} sujet(s) dans cette categorie.</p>
                            @if ($category->description)
                                <p class="mt-3 max-w-3xl text-sm leading-7 text-stone-600">{{ $category->description }}</p>
                            @endif
                            @if ($category->latestTopic)
                                <p class="mt-3 text-sm text-stone-500">
                                    Dernier sujet:
                                    <a href="{{ route('topics.show', $category->latestTopic) }}" class="font-semibold text-stone-700 transition hover:text-[var(--brand-deep)]">
                                        {{ $category->latestTopic->title }}
                                    </a>
                                    ·
                                    <x-user-link :user="$category->latestTopic->user" class="font-semibold text-stone-700">
                                        {{ $category->latestTopic->user->name }}
                                    </x-user-link>
                                </p>
                            @endif
                        </div>
                        <a href="{{ route('categories.show', $category) }}" class="inline-flex items-center rounded-full bg-[var(--brand)] px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-white shadow-[0_18px_35px_rgba(79,70,229,0.28)] transition hover:-translate-y-0.5 hover:bg-[var(--brand-deep)]">
                            Voir les sujets
                        </a>
                    </div>
                </article>
            @empty
                <div class="glass-panel rounded-[2rem] border-dashed p-12 text-center">
                    <p class="section-kicker">Aucune categorie</p>
                    <h3 class="mt-3 text-3xl font-semibold text-stone-950">Aucune categorie disponible</h3>
                    <p class="muted-copy mt-3 text-base">Les grands espaces du forum apparaitront ici au fur et a mesure de leur creation.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
