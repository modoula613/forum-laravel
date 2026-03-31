<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Administration</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Tableau de bord admin</h2>
            <p class="mt-3 text-sm text-stone-500">
                Vue rapide sur la moderation, les inscriptions et les derniers contenus du forum.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-4">
                <article class="glass-panel-strong rounded-[2rem] p-6">
                    <p class="section-kicker">Utilisateurs</p>
                    <p class="mt-4 text-5xl font-semibold text-stone-950">{{ $usersCount }}</p>
                </article>
                <article class="glass-panel-strong rounded-[2rem] p-6">
                    <p class="section-kicker">Sujets</p>
                    <p class="mt-4 text-5xl font-semibold text-stone-950">{{ $topicsCount }}</p>
                </article>
                <article class="glass-panel-strong rounded-[2rem] p-6">
                    <p class="section-kicker">Reponses</p>
                    <p class="mt-4 text-5xl font-semibold text-stone-950">{{ $repliesCount }}</p>
                </article>
                <article class="glass-panel-strong rounded-[2rem] p-6">
                    <p class="section-kicker">Signalements</p>
                    <p class="mt-4 text-5xl font-semibold text-stone-950">{{ $reportsCount }}</p>
                </article>
                <article class="glass-panel-strong rounded-[2rem] p-6">
                    <p class="section-kicker">Tags</p>
                    <p class="mt-4 text-5xl font-semibold text-stone-950">{{ $tagsCount }}</p>
                </article>
                <article class="glass-panel-strong rounded-[2rem] p-6">
                    <p class="section-kicker">Categories</p>
                    <p class="mt-4 text-5xl font-semibold text-stone-950">{{ $categoriesCount }}</p>
                </article>
                <article class="glass-panel-strong rounded-[2rem] p-6">
                    <p class="section-kicker">Logs admin</p>
                    <p class="mt-4 text-5xl font-semibold text-stone-950">{{ $adminLogsCount }}</p>
                </article>
            </div>

            <div class="grid gap-6 lg:grid-cols-4">
                <section class="glass-panel rounded-[2rem] p-6">
                    <p class="section-kicker">Signalements</p>
                    <h3 class="mt-3 text-2xl font-semibold text-stone-950">Derniers signalements</h3>
                    <div class="mt-5 space-y-3">
                        @forelse ($latestReports as $report)
                            <div class="rounded-[1.5rem] bg-white/70 px-4 py-3 text-sm text-stone-700">
                                <p class="font-semibold text-stone-900">{{ $report->user?->name ?? 'Membre' }}</p>
                                <p class="mt-1">{{ $report->reason }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-stone-500">Aucun signalement recent.</p>
                        @endforelse
                    </div>
                </section>

                <section class="glass-panel rounded-[2rem] p-6">
                    <p class="section-kicker">Inscriptions</p>
                    <h3 class="mt-3 text-2xl font-semibold text-stone-950">Derniers utilisateurs</h3>
                    <div class="mt-5 space-y-3">
                        @forelse ($latestUsers as $user)
                            <div class="rounded-[1.5rem] bg-white/70 px-4 py-3">
                                <a href="{{ route('users.show', $user) }}" class="font-semibold text-stone-900 transition hover:text-[var(--brand-deep)]">
                                    {{ $user->name }}
                                </a>
                                <p class="mt-1 text-sm text-stone-500">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-stone-500">Aucun utilisateur recent.</p>
                        @endforelse
                    </div>
                </section>

                <section class="glass-panel rounded-[2rem] p-6">
                    <p class="section-kicker">Publications</p>
                    <h3 class="mt-3 text-2xl font-semibold text-stone-950">Derniers sujets</h3>
                    <div class="mt-5 space-y-3">
                        @forelse ($latestTopics as $topic)
                            <div class="rounded-[1.5rem] bg-white/70 px-4 py-3">
                                <a href="{{ route('topics.show', $topic) }}" class="font-semibold text-stone-900 transition hover:text-[var(--brand-deep)]">
                                    {{ $topic->title }}
                                </a>
                                <p class="mt-1 text-sm text-stone-500">{{ $topic->user->name }} · {{ $topic->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-stone-500">Aucun sujet recent.</p>
                        @endforelse
                    </div>
                </section>

                <section class="glass-panel rounded-[2rem] p-6">
                    <p class="section-kicker">Categories</p>
                    <h3 class="mt-3 text-2xl font-semibold text-stone-950">Dernieres categories</h3>
                    <div class="mt-5 space-y-3">
                        @forelse ($latestCategories as $category)
                            <div class="rounded-[1.5rem] bg-white/70 px-4 py-3">
                                <a href="{{ route('categories.show', $category) }}" class="font-semibold text-stone-900 transition hover:text-[var(--brand-deep)]">
                                    {{ $category->name }}
                                </a>
                                <p class="mt-1 text-sm text-stone-500">{{ $category->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-stone-500">Aucune categorie recente.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
