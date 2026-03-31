<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Administration</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Signalements</h2>
            <p class="muted-copy mt-3 text-base leading-7">
                Consulte les contenus signales par les membres pour faciliter la moderation.
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

            @forelse ($reports as $report)
                <article class="glass-panel rounded-[2rem] p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="space-y-3">
                            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">
                                <span class="rounded-full bg-rose-100 px-3 py-1 text-rose-600">
                                    {{ $report->topic_id ? 'Sujet' : 'Reponse' }}
                                </span>
                                <span>Signale par {{ $report->user->name }}</span>
                                <span class="rounded-full px-3 py-1 {{ $report->status === 'resolved' ? 'bg-emerald-100 text-emerald-700' : ($report->status === 'ignored' ? 'bg-stone-200 text-stone-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ $report->status === 'resolved' ? 'Traite' : ($report->status === 'ignored' ? 'Ignore' : 'En attente') }}
                                </span>
                            </div>
                            <h3 class="text-2xl font-semibold text-stone-950">
                                @if ($report->topic)
                                    {{ $report->topic->title }}
                                @elseif ($report->reply && $report->reply->topic)
                                    {{ $report->reply->topic->title }}
                                @else
                                    Contenu supprime
                                @endif
                            </h3>
                            @if ($report->reply)
                                <div class="rounded-[1.5rem] bg-white/70 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">Contenu de la reponse</p>
                                    <p class="mt-3 text-sm leading-7 text-stone-700">{{ $report->reply->content }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-4 text-sm text-stone-600">
                                    <span>
                                        Auteur de la reponse :
                                        <span class="font-semibold text-stone-900">{{ $report->reply->user->name }}</span>
                                    </span>
                                    <span>
                                        Utilisateur qui signale :
                                        <span class="font-semibold text-stone-900">{{ $report->user->name }}</span>
                                    </span>
                                </div>
                            @endif
                            <p class="text-sm text-stone-600">
                                Raison du signalement :
                                <span class="font-medium text-stone-900">{{ $report->reason ?: 'Aucun motif precise.' }}</span>
                            </p>
                        </div>
                        <div class="space-y-3 text-right text-sm text-stone-500">
                            <p>{{ $report->created_at->format('d/m/Y H:i') }}</p>
                            @if ($report->topic)
                                <a href="{{ route('topics.show', $report->topic) }}" class="inline-flex rounded-full bg-[var(--brand)] px-4 py-2 font-semibold text-white transition hover:bg-[var(--brand-deep)]">
                                    Voir le sujet
                                </a>
                            @elseif ($report->reply && $report->reply->topic)
                                <a href="{{ route('topics.show', $report->reply->topic) }}" class="inline-flex rounded-full bg-[var(--brand)] px-4 py-2 font-semibold text-white transition hover:bg-[var(--brand-deep)]">
                                    Voir la discussion
                                </a>
                            @endif
                            <div class="flex flex-wrap justify-end gap-2">
                                @if ($report->topic)
                                    <form method="POST" action="{{ route('admin.reports.destroyTopic', $report) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-full bg-rose-600 px-4 py-2 font-semibold text-white transition hover:bg-rose-500">
                                            Supprimer le sujet
                                        </button>
                                    </form>
                                @elseif ($report->reply)
                                    <form method="POST" action="{{ route('admin.reports.destroyReply', $report) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-full bg-rose-600 px-4 py-2 font-semibold text-white transition hover:bg-rose-500">
                                            Supprimer la reponse
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.reports.resolve', $report) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 font-semibold text-emerald-700 transition hover:bg-emerald-100">
                                        Marquer traite
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.reports.ignore', $report) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-full border border-stone-200 bg-stone-100 px-4 py-2 font-semibold text-stone-700 transition hover:bg-stone-200">
                                        Ignorer
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.reports.destroy', $report) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-2 font-semibold text-stone-700 transition hover:bg-white">
                                        Supprimer le signalement
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="glass-panel rounded-[2rem] border-dashed p-12 text-center">
                    <p class="section-kicker">Aucun signalement</p>
                    <h3 class="mt-3 text-3xl font-semibold text-stone-950">Tout est calme</h3>
                </div>
            @endforelse

            <div class="glass-panel rounded-[2rem] px-4 py-4 sm:px-6">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
