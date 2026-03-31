<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Activite du membre</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">{{ $user->name }}</h2>
            <p class="muted-copy mt-3 text-base leading-7">
                Les dernieres actions publiques de ce membre sur Sphere.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div>
                <a href="{{ route('users.show', $user) }}" class="text-sm font-semibold text-[var(--brand)] transition hover:text-[var(--brand-deep)]">
                    Retour au profil
                </a>
            </div>

            <div class="space-y-4">
                @forelse ($activities as $activity)
                    <article class="glass-panel rounded-[2rem] p-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[var(--brand)]">
                                    {{ $activity->type }}
                                </p>
                                <p class="mt-3 text-base text-stone-700">
                                    {{ $activity->description }}
                                </p>
                            </div>
                            <p class="text-sm text-stone-500">
                                {{ $activity->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </article>
                @empty
                    <div class="glass-panel rounded-[2rem] border-dashed p-8 text-center text-sm text-stone-500">
                        Aucune activite enregistree pour le moment.
                    </div>
                @endforelse
            </div>

            <div class="glass-panel rounded-[2rem] px-4 py-4 sm:px-6">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
