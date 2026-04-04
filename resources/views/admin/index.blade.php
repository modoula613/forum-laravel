<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="section-kicker">Administration</p>
                <h2 class="mt-3 text-4xl font-semibold text-stone-950">Poste de pilotage</h2>
                <p class="mt-3 text-sm text-stone-500">
                    Un ecran court pour traiter les urgences, surveiller l'etat du forum et aller vite vers les bons outils.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.reports.index') }}" class="rounded-full bg-rose-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-rose-500">
                    Voir les signalements
                </a>
                <a href="{{ route('admin.users.index') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-white">
                    Utilisateurs
                </a>
                <a href="{{ route('admin.topics.index') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-white">
                    Sujets
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="grid gap-4 lg:grid-cols-3">
                <a href="{{ route('admin.reports.index') }}" class="glass-panel rounded-[2rem] p-6 transition hover:-translate-y-0.5 hover:bg-white/80">
                    <p class="section-kicker">Urgent</p>
                    <p class="mt-3 text-4xl font-semibold text-stone-950">{{ $pendingReportsCount }}</p>
                    <p class="mt-2 text-sm text-stone-500">signalement(s) en attente</p>
                </a>
                <a href="{{ route('admin.users.index', ['banned' => 1]) }}" class="glass-panel rounded-[2rem] p-6 transition hover:-translate-y-0.5 hover:bg-white/80">
                    <p class="section-kicker">Moderation</p>
                    <p class="mt-3 text-4xl font-semibold text-stone-950">{{ $bannedUsersCount }}</p>
                    <p class="mt-2 text-sm text-stone-500">utilisateur(s) bannis</p>
                </a>
                <div class="glass-panel rounded-[2rem] p-6">
                    <p class="section-kicker">Forum</p>
                    <p class="mt-3 text-lg font-semibold text-stone-950">Vue rapide</p>
                    <p class="mt-2 text-sm text-stone-500">
                        {{ $usersCount }} utilisateurs · {{ $topicsCount }} sujets · {{ $reportsCount }} signalements
                    </p>
                </div>
            </section>

            <section class="glass-panel rounded-[2rem] p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="section-kicker">Traitement</p>
                        <h3 class="mt-3 text-3xl font-semibold text-stone-950">Derniers signalements</h3>
                    </div>
                    <p class="text-sm text-stone-500">
                        {{ $resolvedReportsCount }} traites · {{ $ignoredReportsCount }} ignores
                    </p>
                </div>
                <div class="mt-5 grid gap-4 lg:grid-cols-2">
                    @forelse ($latestReports as $report)
                        <div class="rounded-[1.5rem] bg-white/70 px-5 py-4 text-sm text-stone-700">
                            <p class="font-semibold text-stone-900">
                                <x-user-link :user="$report->user">
                                    {{ $report->user?->name ?? 'Membre' }}
                                </x-user-link>
                            </p>
                            <p class="mt-2">{{ $report->reason }}</p>
                            @if ($report->reply?->content)
                                <p class="mt-2 text-stone-500">{{ \Illuminate\Support\Str::limit($report->reply->content, 100) }}</p>
                            @elseif ($report->topic?->title)
                                <p class="mt-2 text-stone-500">{{ $report->topic->title }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-stone-500">Aucun signalement recent.</p>
                    @endforelse
                </div>
            </section>

            <div class="grid gap-6 lg:grid-cols-2">
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
                                <p class="mt-1 text-sm text-stone-500">
                                    <x-user-link :user="$topic->user">
                                        {{ $topic->user->name }}
                                    </x-user-link>
                                    · {{ $topic->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        @empty
                            <p class="text-sm text-stone-500">Aucun sujet recent.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
