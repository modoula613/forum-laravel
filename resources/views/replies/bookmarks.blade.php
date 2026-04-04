<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Signets</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Mes reponses sauvegardees</h2>
            <p class="muted-copy mt-3 text-base leading-7">
                Retrouve ici les reponses que tu as mises de cote pour y revenir plus tard.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="glass-panel rounded-[2rem] p-5 sm:p-6">
                <form method="GET" action="{{ route('replies.bookmarks') }}" class="grid gap-4 lg:grid-cols-[1fr_1fr_1fr_auto] lg:items-end">
                    <div>
                        <label for="search" class="mb-2 block text-sm font-semibold uppercase tracking-[0.16em] text-stone-600">
                            Mot-cle
                        </label>
                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Rechercher dans mes reponses sauvegardees..."
                            class="block w-full rounded-[1.25rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                        >
                    </div>
                    <div>
                        <label for="topic" class="mb-2 block text-sm font-semibold uppercase tracking-[0.16em] text-stone-600">
                            Sujet
                        </label>
                        <input
                            id="topic"
                            type="text"
                            name="topic"
                            value="{{ request('topic') }}"
                            placeholder="Filtrer par sujet..."
                            class="block w-full rounded-[1.25rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                        >
                    </div>
                    <div>
                        <label for="author" class="mb-2 block text-sm font-semibold uppercase tracking-[0.16em] text-stone-600">
                            Auteur
                        </label>
                        <input
                            id="author"
                            type="text"
                            name="author"
                            value="{{ request('author') }}"
                            placeholder="Filtrer par auteur..."
                            class="block w-full rounded-[1.25rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                        >
                    </div>
                    <div class="flex items-center gap-3">
                        @if (request()->filled('search') || request()->filled('topic') || request()->filled('author'))
                            <a href="{{ route('replies.bookmarks') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-white">
                                Reinitialiser
                            </a>
                        @endif
                        <x-primary-button>Filtrer</x-primary-button>
                    </div>
                </form>
            </div>

            @forelse ($replies as $reply)
                <article class="glass-panel-strong rounded-[2rem] p-6">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        <div class="space-y-3">
                            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">
                                <span class="rounded-full bg-[rgba(79,70,229,0.12)] px-3 py-1 text-[var(--brand)]">Reponse</span>
                                <span>Publiee le {{ $reply->created_at->format('d/m/Y H:i') }}</span>
                                @if ($reply->pivot?->created_at)
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-700">
                                        Sauvegardee le {{ $reply->pivot->created_at->format('d/m/Y H:i') }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-2xl font-semibold text-stone-950">
                                <a href="{{ route('topics.show', $reply->topic) }}" class="transition hover:text-[var(--brand-deep)]">
                                    {{ $reply->topic->title }}
                                </a>
                            </h3>
                            <p class="text-sm text-stone-500">
                                Par
                                <x-user-link :user="$reply->user" class="font-semibold text-stone-700">
                                    {{ $reply->user->name }}
                                </x-user-link>
                            </p>
                            <p class="muted-copy text-base leading-8">
                                {{ $reply->content }}
                            </p>
                        </div>
                        <a href="{{ route('topics.show', $reply->topic) }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-white">
                            Voir le sujet
                        </a>
                    </div>
                </article>
            @empty
                <div class="glass-panel rounded-[2rem] border-dashed p-12 text-center">
                    <p class="section-kicker">Aucun signet</p>
                    <h3 class="mt-3 text-3xl font-semibold text-stone-950">Aucune reponse sauvegardee</h3>
                    <p class="muted-copy mt-3 text-base">Sauvegarde des reponses depuis les discussions pour les retrouver ici.</p>
                </div>
            @endforelse

            <div class="glass-panel rounded-[2rem] px-4 py-4 sm:px-6">
                {{ $replies->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
