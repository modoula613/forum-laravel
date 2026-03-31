<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Badges du membre</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">{{ $user->name }}</h2>
            <p class="muted-copy mt-3 text-base leading-7">
                Les recompenses obtenues par ce membre sur Sphere.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('users.show', $user) }}" class="text-sm font-semibold text-[var(--brand)] transition hover:text-[var(--brand-deep)]">
                    Retour au profil
                </a>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($badges as $badge)
                    <article class="glass-panel rounded-[2rem] p-6">
                        <p class="section-kicker">Obtenu</p>
                        <h3 class="mt-3 text-2xl font-semibold text-stone-950">{{ $badge->name }}</h3>
                        <p class="mt-4 text-sm leading-7 text-stone-600">
                            {{ $badge->description ?: 'Aucune description disponible pour ce badge.' }}
                        </p>
                    </article>
                @empty
                    <div class="glass-panel rounded-[2rem] border-dashed p-8 text-center text-sm text-stone-500 md:col-span-2 xl:col-span-3">
                        Aucun badge obtenu pour le moment.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
