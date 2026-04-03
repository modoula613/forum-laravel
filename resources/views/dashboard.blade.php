<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <p class="section-kicker">Mon espace</p>
                <h2 class="mt-3 text-4xl font-semibold text-stone-950">Tableau de bord</h2>
                <p class="muted-copy mt-3 text-base leading-7">
                    Un point d'entree simple pour retrouver tes suivis, tes alertes et les discussions qui te correspondent.
                </p>
            </div>
            <a
                href="{{ route('topics.index') }}"
                class="inline-flex items-center rounded-full bg-[var(--brand)] px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-white shadow-[0_18px_35px_rgba(79,70,229,0.28)] transition hover:-translate-y-0.5 hover:bg-[var(--brand-deep)]"
            >
                Retour au forum
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-8 px-4 sm:px-6 lg:px-8">
            <section class="glass-panel rounded-[2rem] p-6 sm:p-7">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="section-kicker">Resume</p>
                        <h3 class="mt-2 text-2xl font-semibold text-stone-950">Ton espace en un coup d'oeil</h3>
                        <p class="mt-2 text-sm leading-6 text-stone-500">
                            Retrouve ce qui t'attend sans passer par une page surchargee de chiffres.
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div class="rounded-[1.4rem] bg-white/80 px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-400">Favoris</p>
                            <p class="mt-2 text-2xl font-semibold text-stone-950">{{ $overview['favorites'] }}</p>
                        </div>
                        <div class="rounded-[1.4rem] bg-white/80 px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-400">Membres suivis</p>
                            <p class="mt-2 text-2xl font-semibold text-stone-950">{{ $overview['following_members'] }}</p>
                        </div>
                        <div class="rounded-[1.4rem] bg-white/80 px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-400">Notifications</p>
                            <p class="mt-2 text-2xl font-semibold text-stone-950">{{ $overview['unread_notifications'] }}</p>
                        </div>
                        <div class="rounded-[1.4rem] bg-white/80 px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-400">Messages</p>
                            <p class="mt-2 text-2xl font-semibold text-stone-950">{{ $overview['unread_messages'] }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="glass-panel rounded-[2.25rem] p-6 sm:p-8">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="section-kicker">Acces rapides</p>
                        <h3 class="mt-3 text-3xl font-semibold text-stone-950">Les essentiels</h3>
                    </div>
                </div>
                <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <a href="{{ route('favorites.index') }}" class="rounded-[1.5rem] bg-white/70 p-5 transition hover:-translate-y-0.5 hover:bg-white">
                        <p class="text-lg font-semibold text-stone-950">Mes favoris</p>
                    </a>
                    <a href="{{ route('topics.feed') }}" class="rounded-[1.5rem] bg-white/70 p-5 transition hover:-translate-y-0.5 hover:bg-white">
                        <p class="text-lg font-semibold text-stone-950">Mon flux</p>
                    </a>
                    <a href="{{ route('tags.followed') }}" class="rounded-[1.5rem] bg-white/70 p-5 transition hover:-translate-y-0.5 hover:bg-white">
                        <p class="text-lg font-semibold text-stone-950">Mes tags suivis</p>
                    </a>
                </div>
            </section>

            <section class="glass-panel rounded-[2.25rem] p-6 sm:p-8">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="section-kicker">Pour toi</p>
                        <h3 class="mt-3 text-3xl font-semibold text-stone-950">Mon flux</h3>
                    </div>
                    <a href="{{ route('topics.feed') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-white">
                        Ouvrir mon flux
                    </a>
                </div>
                <div class="mt-6 grid gap-4">
                    @forelse ($recommendedTopics as $topic)
                        <article class="rounded-[1.5rem] bg-white/70 p-5">
                            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-700">Recommande</span>
                            </div>
                            <h4 class="mt-3 text-xl font-semibold text-stone-950">
                                <a href="{{ route('topics.show', $topic) }}" class="transition hover:text-[var(--brand-deep)]">
                                    {{ $topic->title }}
                                </a>
                            </h4>
                            <p class="mt-2 text-sm text-stone-500">
                                Par <span class="font-semibold text-stone-700">{{ $topic->user->name }}</span>
                            </p>
                            <div class="mt-4">
                                <a href="{{ route('topics.show', $topic) }}" class="text-sm font-semibold text-[var(--brand-deep)] transition hover:text-[var(--brand)]">
                                    Ouvrir la discussion
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-[1.5rem] bg-white/70 p-6 text-sm text-stone-500">
                            Suis quelques membres pour voir ici uniquement leurs derniers sujets.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
