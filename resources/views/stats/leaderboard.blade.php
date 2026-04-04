<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Classement</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Leaderboard Sphere</h2>
            <p class="muted-copy mt-3 text-base leading-7">
                Les membres les plus actifs et les plus reconnus de la communaute.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            @forelse ($users as $user)
                <article class="glass-panel rounded-[2rem] p-5 sm:p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex items-center gap-4">
                            <x-user-avatar :user="$user" class="h-12 w-12 bg-[var(--brand)] text-sm font-semibold uppercase text-white shadow-[0_10px_24px_rgba(79,70,229,0.22)]" />
                            <div>
                                <a href="{{ route('users.show', $user) }}" class="text-xl font-semibold text-stone-950 transition hover:text-[var(--brand-deep)]">
                                    {{ $user->name }}
                                </a>
                                <p class="mt-1 text-sm text-stone-500">Lvl {{ $user->level }} · Rep {{ $user->reputation }}</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 text-sm">
                            <span class="rounded-full bg-white/80 px-4 py-2 font-medium text-stone-700">{{ $user->topics_count }} sujets</span>
                            <span class="rounded-full bg-[rgba(79,70,229,0.12)] px-4 py-2 font-medium text-[var(--brand)]">{{ $user->replies_count }} reponses</span>
                            <span class="rounded-full bg-amber-100 px-4 py-2 font-medium text-amber-700">Rep {{ $user->reputation }}</span>
                        </div>
                    </div>
                </article>
            @empty
                <div class="glass-panel rounded-[2rem] border-dashed p-8 text-center text-sm text-stone-500">
                    Aucun utilisateur a classer.
                </div>
            @endforelse

            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
