<x-app-layout>
    <x-slot name="header">
        <div class="max-w-2xl">
            <p class="section-kicker">Notifications</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Activite recente</h2>
            <p class="muted-copy mt-3 text-base leading-7">
                Retrouve ici les avertissements et les evenements lies a ton compte.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            @forelse (auth()->user()->notifications as $notification)
                <article class="glass-panel-strong rounded-[2rem] p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-2xl font-semibold text-stone-950">
                                {{ $notification->data['title'] ?? 'Notification' }}
                            </h3>
                            <p class="muted-copy mt-2 text-base leading-7">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            @if (($notification->data['type'] ?? null) === 'new_reply')
                                <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-stone-600">
                                    <span class="rounded-full bg-[rgba(79,70,229,0.12)] px-3 py-1 font-semibold text-[var(--brand)]">
                                        {{ $notification->data['reply_user'] ?? 'Un membre' }}
                                    </span>
                                    <span>sur</span>
                                    <span class="font-semibold text-stone-900">{{ $notification->data['topic_title'] ?? 'Sujet' }}</span>
                                </div>
                                @if (! empty($notification->data['url']))
                                    <a href="{{ $notification->data['url'] }}" class="mt-4 inline-flex items-center rounded-full bg-[var(--brand)] px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-white transition hover:bg-[var(--brand-deep)]">
                                        Voir la discussion
                                    </a>
                                @endif
                            @elseif (($notification->data['type'] ?? null) === 'new_private_message')
                                <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-stone-600">
                                    <span class="rounded-full bg-[rgba(139,92,246,0.12)] px-3 py-1 font-semibold text-[var(--brand)]">
                                        {{ $notification->data['sender_name'] ?? 'Un membre' }}
                                    </span>
                                    <span>a demarre une conversation privee avec vous.</span>
                                </div>
                                @if (! empty($notification->data['url']))
                                    <a href="{{ $notification->data['url'] }}" class="mt-4 inline-flex items-center rounded-full bg-[var(--brand)] px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-white transition hover:bg-[var(--brand-deep)]">
                                        Ouvrir la conversation
                                    </a>
                                @endif
                            @elseif (($notification->data['type'] ?? null) === 'new_topic_followed_tag')
                                <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-stone-600">
                                    <span class="rounded-full bg-amber-100 px-3 py-1 font-semibold text-amber-700">
                                        {{ $notification->data['tag_name'] ?? 'Tag' }}
                                    </span>
                                    <span class="font-semibold text-stone-900">{{ $notification->data['topic_title'] ?? 'Sujet' }}</span>
                                </div>
                                @if (! empty($notification->data['url']))
                                    <a href="{{ $notification->data['url'] }}" class="mt-4 inline-flex items-center rounded-full bg-amber-500 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-white transition hover:bg-amber-400">
                                        Voir le sujet
                                    </a>
                                @endif
                            @elseif (($notification->data['type'] ?? null) === 'reply_reported')
                                <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-stone-600">
                                    <span class="rounded-full bg-rose-100 px-3 py-1 font-semibold text-rose-700">
                                        Moderation
                                    </span>
                                    <span class="font-semibold text-stone-900">{{ $notification->data['topic_title'] ?? 'Sujet' }}</span>
                                </div>
                                <p class="mt-3 text-sm leading-7 text-stone-600">
                                    {{ $notification->data['reply_content'] ?? '' }}
                                </p>
                                <p class="mt-2 text-sm text-stone-600">
                                    Motif: <span class="font-medium text-stone-900">{{ $notification->data['reason'] ?? '' }}</span>
                                </p>
                                @if (! empty($notification->data['url']))
                                    <a href="{{ $notification->data['url'] }}" class="mt-4 inline-flex items-center rounded-full bg-rose-600 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-white transition hover:bg-rose-500">
                                        Voir la discussion
                                    </a>
                                @endif
                            @endif
                        </div>
                        <div class="text-right">
                            @if (array_key_exists('warning_count', $notification->data))
                                <div class="rounded-full bg-[rgba(79,70,229,0.12)] px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-[var(--brand)]">
                                    {{ ($notification->data['warning_count'] ?? 0) }} avertissement(s)
                                </div>
                            @elseif (($notification->data['type'] ?? null) === 'new_private_message')
                                <div class="rounded-full bg-[rgba(139,92,246,0.12)] px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-[var(--brand)]">
                                    Message prive
                                </div>
                            @elseif (($notification->data['type'] ?? null) === 'new_topic_followed_tag')
                                <div class="rounded-full bg-amber-100 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">
                                    Sujet recommande
                                </div>
                            @elseif (($notification->data['type'] ?? null) === 'reply_reported')
                                <div class="rounded-full bg-rose-100 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-rose-700">
                                    Signalement
                                </div>
                            @else
                                <div class="rounded-full bg-emerald-100 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">
                                    Nouvelle reponse
                                </div>
                            @endif
                            <p class="mt-3 text-xs text-stone-500">
                                {{ $notification->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                </article>
            @empty
                <div class="glass-panel rounded-[2.25rem] border-dashed p-12 text-center">
                    <p class="section-kicker">Aucune notification</p>
                    <h3 class="mt-3 text-3xl font-semibold text-stone-950">Rien a signaler</h3>
                    <p class="muted-copy mt-3 text-base">Tes notifications apparaitront ici lorsqu'un evenement concernera ton compte.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
