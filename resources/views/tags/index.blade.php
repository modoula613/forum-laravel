<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Tags</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Explorer les tags</h2>
            <p class="muted-copy mt-3 text-base leading-7">
                Retrouve les sujets regroupes par technologies, themes et mots-clefs utilises par la communaute.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="glass-panel rounded-[2rem] p-5 sm:p-6">
                <form method="GET" action="{{ route('tags.index') }}" class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div class="w-full max-w-xl">
                        <label for="search" class="mb-2 block text-sm font-semibold uppercase tracking-[0.16em] text-stone-600">
                            Recherche
                        </label>
                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Rechercher un tag..."
                            class="block w-full rounded-[1.25rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                        >
                    </div>
                    <div class="flex items-center gap-3">
                        @if (request()->filled('search'))
                            <a href="{{ route('tags.index') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-white">
                                Reinitialiser
                            </a>
                        @endif
                        <x-primary-button>Rechercher</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($tags as $tag)
                    <a href="{{ route('tags.show', $tag) }}" class="glass-panel-strong rounded-[2rem] p-6 transition hover:-translate-y-1 hover:shadow-[0_26px_60px_rgba(57,72,120,0.16)]">
                        <p class="section-kicker">Tag</p>
                        <h3 class="mt-3 text-2xl font-semibold text-stone-950">{{ $tag->name }}</h3>
                        <p class="mt-3 text-sm text-stone-500">{{ $tag->topics_count }} sujet(s)</p>
                        <p class="mt-2 text-sm text-stone-500">{{ $tag->followers_count }} abonne(s)</p>
                        <p class="mt-4 text-sm leading-7 text-stone-600">
                            Un point d’entree rapide pour retrouver des discussions proches et suivre ce qui revient souvent.
                        </p>
                    </a>
                @empty
                    <div class="glass-panel rounded-[2rem] border-dashed p-12 text-center md:col-span-2 xl:col-span-3">
                        <p class="section-kicker">Aucun tag</p>
                        <h3 class="mt-3 text-3xl font-semibold text-stone-950">Aucun tag disponible</h3>
                    </div>
                @endforelse
            </div>

            <section class="glass-panel rounded-[2rem] p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-3xl">
                        <p class="section-kicker">Suivi</p>
                        <h3 class="mt-3 text-3xl font-semibold text-stone-950">Les tags servent aussi a personnaliser ton experience</h3>
                        <p class="muted-copy mt-3 text-base leading-7">
                            En suivant un tag, tu fais remonter les sujets correspondants dans ton flux et tu reçois des notifications quand une nouvelle discussion apparait.
                        </p>
                    </div>
                    @auth
                        <a href="{{ route('tags.followed') }}" class="rounded-full bg-[var(--brand)] px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-white shadow-[0_18px_35px_rgba(79,70,229,0.28)] transition hover:-translate-y-0.5 hover:bg-[var(--brand-deep)]">
                            Voir mes tags suivis
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-stone-700 transition hover:bg-white">
                            Se connecter
                        </a>
                    @endauth
                </div>
            </section>

            <div class="glass-panel rounded-[2rem] px-4 py-4 sm:px-6">
                {{ $tags->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
