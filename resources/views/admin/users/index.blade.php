<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="section-kicker">Administration</p>
                <h2 class="mt-3 text-4xl font-semibold text-stone-950">Gestion des utilisateurs</h2>
                <p class="mt-3 text-sm text-stone-500">
                    Gere les comptes, surveille leur participation et bloque ou debloque les membres si necessaire.
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
                        <label for="search" class="sr-only">Rechercher un utilisateur</label>
                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Rechercher un utilisateur"
                            class="w-full rounded-[1.25rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                        >
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('admin.users.index', ['banned' => 1, 'search' => request('search')]) }}" class="rounded-full border px-4 py-2 text-sm font-semibold transition {{ request('banned') === '1' ? 'border-stone-900 bg-stone-900 text-white' : 'border-[rgba(71,85,135,0.16)] bg-white/80 text-stone-700 hover:bg-white' }}">
                            Voir seulement les bannis
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                            Reinitialiser
                        </a>
                    </div>
                </form>
            </section>

            <section class="space-y-4">
                @forelse ($users as $user)
                    <article class="glass-panel rounded-[2rem] p-5 sm:p-6">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex items-center gap-4">
                                <x-user-avatar :user="$user" class="h-12 w-12 bg-[var(--brand)] text-sm font-semibold uppercase text-white shadow-[0_10px_24px_rgba(79,70,229,0.22)]" />
                                <div>
                                    <h3 class="text-xl font-semibold text-stone-950">
                                        <x-user-link :user="$user">
                                            {{ $user->name }}
                                        </x-user-link>
                                    </h3>
                                    <p class="mt-1 text-sm text-stone-500">{{ $user->email }}</p>
                                    <p class="mt-1 text-sm text-stone-500">Inscrit le {{ $user->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 text-sm">
                                <span class="rounded-full bg-white/80 px-4 py-2 font-medium text-stone-700">{{ $user->topics_count }} sujets</span>
                                <span class="rounded-full bg-[rgba(79,70,229,0.12)] px-4 py-2 font-medium text-[var(--brand)]">{{ $user->replies_count }} reponses</span>
                                <span class="rounded-full px-4 py-2 font-medium {{ $user->is_blocked ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $user->is_blocked ? 'Bloque' : 'Actif' }}
                                </span>
                                <span class="rounded-full px-4 py-2 font-medium {{ $user->is_banned ? 'bg-stone-900 text-white' : 'bg-white/80 text-stone-700' }}">
                                    {{ $user->is_banned ? 'Banni' : 'Non banni' }}
                                </span>
                                <a href="{{ route('users.show', $user) }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-2 font-semibold text-stone-700 transition hover:bg-white">
                                    Voir profil
                                </a>
                                <form method="POST" action="{{ route('admin.users.toggleBlock', $user) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        class="rounded-full px-4 py-2 font-semibold text-white transition {{ $user->is_blocked ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-rose-600 hover:bg-rose-500' }}"
                                    >
                                        {{ $user->is_blocked ? 'Debloquer' : 'Bloquer' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ $user->is_banned ? route('admin.users.unban', $user) : route('admin.users.ban', $user) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        class="rounded-full px-4 py-2 font-semibold text-white transition {{ $user->is_banned ? 'bg-[var(--brand)] hover:bg-[var(--brand-deep)]' : 'bg-stone-900 hover:bg-stone-800' }}"
                                    >
                                        {{ $user->is_banned ? 'Debannir' : 'Bannir' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="glass-panel rounded-[2rem] border-dashed p-8 text-center text-sm text-stone-500">
                        Aucun utilisateur disponible.
                    </div>
                @endforelse
            </section>

            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
