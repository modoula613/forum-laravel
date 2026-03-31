<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Administration</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Gestion des badges</h2>
            <p class="muted-copy mt-3 text-base leading-7">
                Consulte les badges disponibles et attribue-les manuellement si necessaire.
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

            <div class="space-y-4">
                @foreach ($badges as $badge)
                    <article class="glass-panel rounded-[2rem] p-6">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h3 class="text-2xl font-semibold text-stone-950">{{ $badge->name }}</h3>
                                    <span class="rounded-full bg-[rgba(79,70,229,0.12)] px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[var(--brand)]">
                                        {{ $badge->users_count }} membre(s)
                                    </span>
                                </div>
                                <p class="text-sm leading-7 text-stone-600">
                                    {{ $badge->description ?: 'Aucune description disponible.' }}
                                </p>
                            </div>

                            <form method="POST" action="{{ route('admin.badges.assign', ['user' => $users->first()?->id ?? 0, 'badge' => $badge]) }}" class="min-w-[18rem] space-y-3" x-data="{ selectedUser: '{{ $users->first()?->id ?? '' }}' }" @submit="$event.target.action = '{{ url('/admin/users') }}/' + selectedUser + '/badges/{{ $badge->id }}'">
                                @csrf
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">Attribuer a</label>
                                    <select x-model="selectedUser" class="mt-2 block w-full rounded-[1rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]" required>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <x-primary-button type="submit">Attribuer</x-primary-button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            {{ $badges->links() }}
        </div>
    </div>
</x-app-layout>
