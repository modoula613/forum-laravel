<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="section-kicker">Messages</p>
                <h2 class="mt-3 text-4xl font-semibold text-stone-950">Boite de reception</h2>
                <p class="muted-copy mt-3 text-base leading-7">
                    Retrouve ici les messages prives recus des autres membres.
                </p>
            </div>
            <a href="{{ route('users.index') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-white">
                Chercher un membre
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="glass-panel rounded-[2rem] p-5 sm:p-6">
                <form method="GET" action="{{ route('messages.index') }}" class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_auto] lg:w-full lg:max-w-3xl">
                        <div>
                            <label for="search" class="mb-2 block text-sm font-semibold uppercase tracking-[0.16em] text-stone-500">
                                Rechercher
                            </label>
                            <input
                                id="search"
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Nom du membre ou contenu du dernier message"
                                class="block w-full rounded-[1.5rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                            >
                        </div>
                        <label class="inline-flex items-center gap-3 rounded-[1.5rem] border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-3 text-sm font-semibold text-stone-700">
                            <input
                                type="checkbox"
                                name="unread"
                                value="1"
                                @checked($unreadOnly)
                                class="rounded border-stone-300 text-[var(--brand)] shadow-sm focus:ring-[var(--brand)]"
                            >
                            Seulement les non lus
                        </label>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        @if ($search !== '' || $unreadOnly)
                            <a href="{{ route('messages.index') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-white">
                                Reinitialiser
                            </a>
                        @endif
                        <x-primary-button>Filtrer</x-primary-button>
                    </div>
                </form>

                <div class="mt-5 flex flex-wrap gap-3 text-sm text-stone-600">
                    <span class="rounded-full bg-white/80 px-4 py-2 font-medium text-stone-700">
                        {{ $conversationSummary['displayed'] ?? $conversations->total() }} conversation(s) affichee(s)
                    </span>
                    <span class="rounded-full bg-[rgba(79,70,229,0.12)] px-4 py-2 font-medium text-[var(--brand)]">
                        {{ $conversationSummary['unread'] }} avec des messages non lus
                    </span>
                </div>
            </section>

            @if (session('success'))
                <div class="glass-panel rounded-[1.5rem] border-emerald-200 bg-emerald-50/90 px-5 py-4 text-sm text-emerald-900">
                    {{ session('success') }}
                </div>
            @endif

            @forelse ($conversations as $conversation)
                <article class="glass-panel rounded-[2rem] p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="flex items-start gap-4">
                            <x-user-avatar :user="$conversation->user" class="mt-1 h-12 w-12 shrink-0 bg-[var(--brand)] text-sm font-semibold uppercase text-white shadow-[0_10px_24px_rgba(79,70,229,0.22)]" />
                            <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-stone-500">Utilisateur concerne</p>
                            <h3 class="mt-2 text-2xl font-semibold text-stone-950">
                                <x-user-link :user="$conversation->user">
                                    {{ $conversation->user->name }}
                                </x-user-link>
                            </h3>
                            <p class="mt-4 whitespace-pre-line text-base leading-8 text-stone-700">{{ $conversation->last_message->content }}</p>
                            </div>
                        </div>
                        <div class="text-right text-sm text-stone-500">
                            <p>{{ $conversation->last_message->created_at->format('d/m/Y H:i') }}</p>
                            @if ($conversation->unread_count > 0)
                                <div class="mt-3">
                                    <span class="inline-flex rounded-full bg-[rgba(139,92,246,0.12)] px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-[var(--brand)]">
                                        Nouveau
                                    </span>
                                </div>
                            @endif
                            <span class="mt-3 inline-flex rounded-full {{ $conversation->unread_count > 0 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }} px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em]">
                                {{ $conversation->unread_count > 0 ? $conversation->unread_count.' non lu(s)' : 'A jour' }}
                            </span>
                            <a href="{{ route('messages.conversation', $conversation->user) }}" class="mt-3 inline-flex rounded-full bg-[var(--brand)] px-4 py-2 font-semibold text-white transition hover:bg-[var(--brand-deep)]">
                                Ouvrir conversation
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="glass-panel rounded-[2rem] border-dashed p-12 text-center">
                    <p class="section-kicker">{{ $search !== '' || $unreadOnly ? 'Aucun resultat' : 'Aucun message' }}</p>
                    <h3 class="mt-3 text-3xl font-semibold text-stone-950">
                        {{ $search !== '' || $unreadOnly ? 'Aucune conversation ne correspond a ces criteres' : 'Ta boite est vide' }}
                    </h3>
                    @if ($search !== '' || $unreadOnly)
                        <p class="mt-3 text-sm text-stone-500">
                            Ajuste les filtres ou reviens a l'affichage complet de ta boite.
                        </p>
                    @endif
                </div>
            @endforelse

            <div class="glass-panel rounded-[2rem] px-4 py-4 sm:px-6">
                {{ $conversations->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
