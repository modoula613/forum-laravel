<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Statistiques</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Vue d'ensemble du forum</h2>
            <p class="muted-copy mt-3 text-base leading-7">
                Les principaux chiffres de Sphere pour suivre l'activite de la communaute.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-6xl gap-6 px-4 sm:px-6 lg:grid-cols-4 lg:px-8">
            <article class="glass-panel-strong rounded-[2rem] p-6">
                <p class="section-kicker">Sujets</p>
                <p class="mt-4 text-5xl font-semibold text-stone-950">{{ $topicsCount }}</p>
            </article>

            <article class="glass-panel-strong rounded-[2rem] p-6">
                <p class="section-kicker">Reponses</p>
                <p class="mt-4 text-5xl font-semibold text-stone-950">{{ $repliesCount }}</p>
            </article>

            <article class="glass-panel-strong rounded-[2rem] p-6">
                <p class="section-kicker">Membres</p>
                <p class="mt-4 text-5xl font-semibold text-stone-950">{{ $usersCount }}</p>
            </article>

            <article class="glass-panel-strong rounded-[2rem] p-6">
                <p class="section-kicker">Actualites</p>
                <p class="mt-4 text-5xl font-semibold text-stone-950">{{ $newsCount }}</p>
            </article>
        </div>

        <div class="mx-auto mt-6 grid max-w-6xl gap-6 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
            <section class="glass-panel rounded-[2rem] p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="section-kicker">Classement</p>
                        <h3 class="mt-3 text-3xl font-semibold text-stone-950">Membres les plus actifs</h3>
                    </div>
                </div>
                <div class="mt-5 space-y-3">
                    @forelse ($topUsers as $user)
                        <div class="flex items-center justify-between gap-4 rounded-[1.5rem] bg-white/70 px-4 py-3">
                            <a href="{{ route('users.show', $user) }}" class="font-semibold text-stone-900 transition hover:text-[var(--brand-deep)]">
                                {{ $user->name }}
                            </a>
                            <span class="text-sm text-stone-600">{{ $user->topics_count }} sujets · {{ $user->replies_count }} reponses</span>
                        </div>
                    @empty
                        <p class="text-sm text-stone-500">Aucun membre a classer.</p>
                    @endforelse
                </div>
                @if ($topTopics->isNotEmpty())
                    <div class="mt-5 border-t border-[rgba(71,85,135,0.12)] pt-5">
                        <p class="text-sm font-medium text-stone-500">Sujet le plus actif</p>
                        <a href="{{ route('topics.show', $topTopics->first()) }}" class="mt-2 inline-flex text-base font-semibold text-stone-900 transition hover:text-[var(--brand-deep)]">
                            {{ $topTopics->first()->title }}
                        </a>
                    </div>
                @endif
            </section>

            <section class="glass-panel rounded-[2rem] p-6">
                <p class="section-kicker">Moderation</p>
                <h3 class="mt-3 text-3xl font-semibold text-stone-950">Etat de la moderation</h3>
                <div class="mt-5 space-y-3">
                    <div class="rounded-[1.5rem] bg-white/70 px-4 py-4 text-sm text-stone-600">
                        <p class="font-medium text-stone-500">Utilisateurs actuellement bannis.</p>
                        <p class="mt-2"><span class="font-semibold text-stone-900">{{ $bannedUsers }}</span> compte(s).</p>
                    </div>
                </div>
            </section>
        </div>

        <div class="mx-auto mt-6 grid max-w-6xl gap-6 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
            <section class="glass-panel rounded-[2rem] p-6">
                <p class="section-kicker">Progression</p>
                <h3 class="mt-3 text-3xl font-semibold text-stone-950">Classement des niveaux</h3>
                <div class="mt-5 space-y-3">
                    @foreach ($topLevels as $user)
                        <div class="flex items-center justify-between gap-4 rounded-[1.5rem] bg-white/70 px-4 py-3">
                            <a href="{{ route('users.show', $user) }}" class="font-semibold text-stone-900 transition hover:text-[var(--brand-deep)]">
                                {{ $user->name }}
                            </a>
                            <span class="text-sm text-stone-600">Lvl {{ $user->level }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="glass-panel rounded-[2rem] p-6">
                <p class="section-kicker">Influence</p>
                <h3 class="mt-3 text-3xl font-semibold text-stone-950">Classement par reputation</h3>
                <div class="mt-5 space-y-3">
                    @foreach ($topReputation as $user)
                        <div class="flex items-center justify-between gap-4 rounded-[1.5rem] bg-white/70 px-4 py-3">
                            <a href="{{ route('users.show', $user) }}" class="font-semibold text-stone-900 transition hover:text-[var(--brand-deep)]">
                                {{ $user->name }}
                            </a>
                            <span class="text-sm text-stone-600">Rep {{ $user->reputation }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="mx-auto mt-6 max-w-6xl px-4 sm:px-6 lg:px-8">
            <section class="glass-panel rounded-[2rem] p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="section-kicker">Tags</p>
                        <h3 class="mt-3 text-3xl font-semibold text-stone-950">Tags populaires</h3>
                    </div>
                    <a href="{{ route('tags.index') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                        Voir tous les tags
                    </a>
                </div>
                <div class="mt-5 flex flex-wrap gap-2">
                    @forelse ($popularTags as $tag)
                        <a href="{{ route('tags.show', $tag) }}" class="rounded-full bg-white/80 px-4 py-2 text-sm font-semibold text-stone-800 transition hover:bg-white">
                            {{ $tag->name }}
                        </a>
                    @empty
                        <p class="text-sm text-stone-500">Aucun tag populaire pour le moment.</p>
                    @endforelse
                </div>
            </section>
        </div>

    </div>
</x-app-layout>
