<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="section-kicker">Administration</p>
                <h2 class="mt-3 text-4xl font-semibold text-stone-950">Gestion des sujets</h2>
                <p class="mt-3 text-sm text-stone-500">
                    Supervise les discussions, applique les actions de moderation et filtre rapidement les cas sensibles.
                </p>
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

            <section class="glass-panel rounded-[2rem] p-5">
                <form method="GET" class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex-1">
                        <label for="search" class="sr-only">Rechercher un sujet</label>
                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Rechercher un sujet"
                            class="w-full rounded-[1.25rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                        >
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('admin.topics.index', ['locked' => 1, 'search' => request('search'), 'pinned' => request('pinned')]) }}" class="rounded-full border px-4 py-2 text-sm font-semibold transition {{ request('locked') === '1' ? 'border-rose-600 bg-rose-600 text-white' : 'border-[rgba(71,85,135,0.16)] bg-white/80 text-stone-700 hover:bg-white' }}">
                            Verrouilles
                        </a>
                        <a href="{{ route('admin.topics.index', ['pinned' => 1, 'search' => request('search'), 'locked' => request('locked')]) }}" class="rounded-full border px-4 py-2 text-sm font-semibold transition {{ request('pinned') === '1' ? 'border-amber-500 bg-amber-500 text-white' : 'border-[rgba(71,85,135,0.16)] bg-white/80 text-stone-700 hover:bg-white' }}">
                            Epingles
                        </a>
                        <a href="{{ route('admin.topics.index') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                            Reinitialiser
                        </a>
                    </div>
                </form>
            </section>

            <section class="space-y-4">
                @forelse ($topics as $topic)
                    <article class="glass-panel rounded-[2rem] p-5 sm:p-6">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <a href="{{ route('topics.show', $topic) }}" class="text-xl font-semibold text-stone-950 transition hover:text-[var(--brand-deep)]">
                                    {{ $topic->title }}
                                </a>
                                <p class="mt-2 text-sm text-stone-500">
                                    {{ $topic->user->name }} · {{ $topic->created_at->format('d/m/Y H:i') }} · {{ $topic->replies_count }} reponses
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold uppercase tracking-[0.16em]">
                                    @if ($topic->is_locked)
                                        <span class="rounded-full bg-rose-100 px-3 py-1 text-rose-700">Verrouille</span>
                                    @endif
                                    @if ($topic->is_pinned)
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-700">Epingle</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-3">
                                <form method="POST" action="{{ route('admin.topics.lock', $topic) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                                        {{ $topic->is_locked ? 'Deverrouiller' : 'Verrouiller' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.topics.pin', $topic) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                                        {{ $topic->is_pinned ? 'Desepingler' : 'Epingler' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.topics.destroy', $topic) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-full bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="glass-panel rounded-[2rem] border-dashed p-8 text-center text-sm text-stone-500">
                        Aucun sujet trouve.
                    </div>
                @endforelse
            </section>

            <div>
                {{ $topics->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
