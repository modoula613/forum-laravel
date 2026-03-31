<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Badges</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Recompenses de la communaute</h2>
            <p class="muted-copy mt-3 text-base leading-7">
                Les badges disponibles sur Sphere et les contributions qu'ils distinguent.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($badges as $badge)
                    <article class="glass-panel rounded-[2rem] p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="section-kicker">Badge</p>
                                <h3 class="mt-3 text-2xl font-semibold text-stone-950">{{ $badge->name }}</h3>
                            </div>
                            <span class="rounded-full bg-[rgba(79,70,229,0.12)] px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[var(--brand)]">
                                {{ $badge->users_count }} membre(s)
                            </span>
                        </div>
                        <p class="mt-4 text-sm leading-7 text-stone-600">
                            {{ $badge->description ?: 'Aucune description disponible pour le moment.' }}
                        </p>
                    </article>
                @empty
                    <div class="glass-panel rounded-[2rem] border-dashed p-8 text-center text-sm text-stone-500 md:col-span-2 xl:col-span-3">
                        Aucun badge disponible pour le moment.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
