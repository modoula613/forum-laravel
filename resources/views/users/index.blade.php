<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="section-kicker">Communaute</p>
                <h2 class="mt-3 text-4xl font-semibold text-stone-950">Les membres de Sphere</h2>
                <p class="mt-3 text-sm text-stone-500">
                    Parcours les membres les plus actifs, leur date d'inscription et leur niveau de participation.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-8 px-4 sm:px-6 lg:px-8">
            <section class="glass-panel rounded-[2rem] p-5 sm:p-6">
                <form method="GET" action="{{ route('users.index') }}" class="flex flex-col gap-4 lg:flex-row">
                    <div class="flex-1">
                        <label for="search" class="sr-only">Rechercher un utilisateur</label>
                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Rechercher un utilisateur..."
                            class="block w-full rounded-[1.5rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                        >
                    </div>
                    <div class="flex items-center gap-3">
                        <x-primary-button>Rechercher</x-primary-button>
                        @if (request()->filled('search'))
                            <a href="{{ route('users.index') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                                Reinitialiser
                            </a>
                        @endif
                    </div>
                </form>
            </section>

            <section class="space-y-4">
                @forelse ($users as $user)
                    <article class="glass-panel rounded-[2rem] p-5 sm:p-6">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex items-center gap-4">
                                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-[var(--brand)] text-sm font-semibold uppercase text-white shadow-[0_10px_24px_rgba(79,70,229,0.22)]">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                                <div>
                                    <h3 class="text-xl font-semibold text-stone-950">
                                        <a href="{{ route('users.show', $user) }}" class="transition hover:text-[var(--brand-deep)]">
                                            {{ $user->name }}
                                        </a>
                                    </h3>
                                    <p class="mt-1 text-sm text-stone-500">Inscrit le {{ $user->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 text-sm">
                                <span class="rounded-full bg-white/80 px-4 py-2 font-medium text-stone-700">{{ $user->topics_count }} sujets</span>
                                <span class="rounded-full bg-[rgba(79,70,229,0.12)] px-4 py-2 font-medium text-[var(--brand)]">{{ $user->replies_count }} reponses</span>
                                <a href="{{ route('users.show', $user) }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-2 font-medium text-stone-700 transition hover:bg-white">
                                    Voir le profil
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="glass-panel rounded-[2rem] border-dashed p-8 text-center text-sm text-stone-500">
                        Aucun utilisateur trouve.
                    </div>
                @endforelse
            </section>

            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
