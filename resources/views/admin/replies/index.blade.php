<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Administration</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Gestion des reponses</h2>
            <p class="mt-3 text-sm text-stone-500">
                Filtre les reponses sensibles, consulte leurs signalements et supprime celles qui doivent l'etre.
            </p>
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
                        <label for="search" class="sr-only">Rechercher une reponse</label>
                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Rechercher une reponse"
                            class="w-full rounded-[1.25rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                        >
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('admin.replies.index', ['reported' => 1, 'search' => request('search')]) }}" class="rounded-full border px-4 py-2 text-sm font-semibold transition {{ request('reported') === '1' ? 'border-rose-600 bg-rose-600 text-white' : 'border-[rgba(71,85,135,0.16)] bg-white/80 text-stone-700 hover:bg-white' }}">
                            Signalees
                        </a>
                        <a href="{{ route('admin.replies.index') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                            Reinitialiser
                        </a>
                    </div>
                </form>
            </section>

            <section class="space-y-4">
                @forelse ($replies as $reply)
                    <article class="glass-panel rounded-[2rem] p-5 sm:p-6">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="max-w-3xl">
                                <p class="text-sm text-stone-500">{{ $reply->user->name }} · {{ $reply->topic->title }} · {{ $reply->created_at->format('d/m/Y H:i') }}</p>
                                <p class="mt-4 whitespace-pre-line text-base leading-8 text-stone-700">{{ $reply->content }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-3">
                                <span class="rounded-full bg-white/80 px-4 py-2 text-sm font-medium text-stone-700">
                                    {{ $reply->reports_count }} signalement(s)
                                </span>
                                @if ($reply->reports_count > 0)
                                    <span class="rounded-full bg-rose-100 px-4 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-rose-700">
                                        Signalee
                                    </span>
                                @endif
                                <form method="POST" action="{{ route('admin.replies.delete', $reply) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-full bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500">
                                        Supprimer la reponse
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="glass-panel rounded-[2rem] border-dashed p-8 text-center text-sm text-stone-500">
                        Aucune reponse trouvee.
                    </div>
                @endforelse
            </section>

            <div>
                {{ $replies->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
