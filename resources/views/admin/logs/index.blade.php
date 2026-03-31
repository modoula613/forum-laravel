<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Administration</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Logs admin</h2>
            <p class="mt-3 text-sm text-stone-500">
                Historique des actions de moderation et de gestion effectuees par les administrateurs.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="glass-panel rounded-[1.5rem] border-emerald-200 bg-emerald-50/90 px-5 py-4 text-sm text-emerald-900">
                    {{ session('success') }}
                </div>
            @endif

            <section class="glass-panel rounded-[2rem] p-5">
                <form method="GET" class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex-1">
                        <label for="search" class="sr-only">Rechercher dans les logs</label>
                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Rechercher dans les logs"
                            class="w-full rounded-[1.25rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                        >
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <select
                            name="action"
                            class="rounded-[1.25rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                        >
                            <option value="">Toutes les actions</option>
                            <option value="delete_reply" @selected(request('action') === 'delete_reply')>Suppression reponse</option>
                            <option value="delete_topic" @selected(request('action') === 'delete_topic')>Suppression sujet</option>
                            <option value="ban_user" @selected(request('action') === 'ban_user')>Bannissement</option>
                            <option value="unban_user" @selected(request('action') === 'unban_user')>Debannissement</option>
                        </select>
                        <x-primary-button>Filtrer</x-primary-button>
                        <a href="{{ route('admin.logs.index') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                            Reinitialiser
                        </a>
                    </div>
                </form>
            </section>

            <section class="flex justify-end">
                <form method="POST" action="{{ route('admin.logs.clear') }}" onsubmit="return confirm('Supprimer tous les logs admin ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-full bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500">
                        Vider les logs
                    </button>
                </form>
            </section>

            @forelse ($logs as $log)
                <article class="glass-panel rounded-[2rem] p-5 sm:p-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-stone-500">{{ $log->admin?->name ?? 'Admin' }}</p>
                            <p class="mt-2 text-base text-stone-800">{{ $log->details }}</p>
                            <p class="mt-2 text-xs uppercase tracking-[0.16em] text-stone-500">{{ $log->action }}</p>
                        </div>
                        <p class="text-sm text-stone-500">{{ $log->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </article>
            @empty
                <div class="glass-panel rounded-[2rem] border-dashed p-8 text-center text-sm text-stone-500">
                    Aucune action admin enregistree.
                </div>
            @endforelse

            <div>
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
