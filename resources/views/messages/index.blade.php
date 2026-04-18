<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="section-kicker">Boite</p>
                <h2 class="mt-3 text-4xl font-semibold text-stone-950">Boite de reception</h2>
                <p class="muted-copy mt-3 text-base leading-7">
                    Un seul endroit pour retrouver tes messages prives et l'activite liee a ton compte.
                </p>
            </div>
            <a href="{{ route('users.index') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-white">
                Chercher un membre
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @php
                $currentUser = auth()->user();
            @endphp

            @if (session('success'))
                <div class="glass-panel rounded-[1.5rem] border-emerald-200 bg-emerald-50/90 px-5 py-4 text-sm text-emerald-900">
                    {{ session('success') }}
                </div>
            @endif

            <section class="overflow-hidden rounded-[2.6rem] border border-[rgba(15,23,42,0.08)] bg-white/95 shadow-[0_28px_80px_rgba(15,23,42,0.08)]">
                <div class="grid gap-0 xl:grid-cols-[minmax(0,1.25fr)_minmax(22rem,0.85fr)]">
                    <div class="border-b border-[rgba(15,23,42,0.08)] xl:border-b-0 xl:border-r xl:border-[rgba(15,23,42,0.08)]">
                        <div class="border-b border-[rgba(15,23,42,0.08)] px-5 py-5 sm:px-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center gap-4">
                                    <x-user-avatar :user="$currentUser" class="h-12 w-12 shrink-0 bg-[var(--brand)] text-sm font-semibold uppercase text-white shadow-[0_10px_24px_rgba(79,70,229,0.22)]" />
                                    <div>
                                        <p class="section-kicker">Messages prives</p>
                                        <div class="mt-2 flex flex-wrap items-center gap-3">
                                            <h3 class="text-2xl font-semibold text-stone-950">{{ $currentUser->name }}</h3>
                                            <span class="rounded-full bg-[rgba(79,70,229,0.12)] px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-[var(--brand)]">
                                                {{ $inboxSummary['messages_unread'] }} non lu(s)
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('users.index') }}" class="inline-flex rounded-full border border-[rgba(15,23,42,0.08)] bg-stone-50 px-4 py-2.5 text-sm font-semibold text-stone-700 transition hover:bg-stone-100">
                                    Chercher un membre
                                </a>
                            </div>

                            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                                <div class="rounded-[1.4rem] bg-stone-50 px-4 py-4">
                                    <p class="text-[0.68rem] font-semibold uppercase tracking-[0.18em] text-stone-400">Messages non lus</p>
                                    <p class="mt-3 text-3xl font-semibold text-stone-950">{{ $inboxSummary['messages_unread'] }}</p>
                                </div>
                                <div class="rounded-[1.4rem] bg-stone-50 px-4 py-4">
                                    <p class="text-[0.68rem] font-semibold uppercase tracking-[0.18em] text-stone-400">Notifications recentes</p>
                                    <p class="mt-3 text-3xl font-semibold text-stone-950">{{ $inboxSummary['notifications_total'] }}</p>
                                </div>
                                <div class="rounded-[1.4rem] bg-[rgba(79,70,229,0.08)] px-4 py-4">
                                    <p class="text-[0.68rem] font-semibold uppercase tracking-[0.18em] text-[var(--brand)]">A traiter</p>
                                    <p class="mt-3 text-3xl font-semibold text-stone-950">{{ $inboxSummary['messages_unread'] + $inboxSummary['notifications_unread'] }}</p>
                                </div>
                            </div>

                            <form method="GET" action="{{ route('messages.index') }}" class="mt-5 space-y-3">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                    <div class="relative flex-1">
                                        <label for="search" class="sr-only">Rechercher</label>
                                        <input
                                            id="search"
                                            type="text"
                                            name="search"
                                            value="{{ $search }}"
                                            placeholder="Rechercher un membre ou un message"
                                            class="block w-full rounded-full border border-[rgba(15,23,42,0.08)] bg-stone-50 px-5 py-3 text-sm text-stone-700 shadow-sm placeholder:text-stone-400 focus:border-[var(--brand)] focus:bg-white focus:ring-[var(--brand)]"
                                        >
                                    </div>
                                    <label class="inline-flex items-center justify-center gap-3 rounded-full border border-[rgba(15,23,42,0.08)] bg-stone-50 px-4 py-3 text-sm font-semibold text-stone-700">
                                        <input
                                            type="checkbox"
                                            name="unread"
                                            value="1"
                                            @checked($unreadOnly)
                                            class="rounded border-stone-300 text-[var(--brand)] shadow-sm focus:ring-[var(--brand)]"
                                        >
                                        Non lus
                                    </label>
                                    <x-primary-button class="justify-center rounded-full px-5 py-3">Filtrer</x-primary-button>
                                </div>
                                <div class="flex flex-wrap items-center justify-between gap-3 text-sm text-stone-500">
                                    <span>{{ $conversationSummary['displayed'] ?? $conversations->total() }} conversation(s) affichee(s)</span>
                                    @if ($search !== '' || $unreadOnly)
                                        <a href="{{ route('messages.index') }}" class="font-semibold text-[var(--brand)] transition hover:text-[var(--brand-deep)]">
                                            Reinitialiser
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>

                        <div class="divide-y divide-[rgba(15,23,42,0.08)]">
                            @forelse ($conversations as $conversation)
                                @php
                                    $lastMessagePreview = \Illuminate\Support\Str::limit($conversation->last_message->content, 95);
                                    $lastMessageTime = $conversation->last_message->created_at->isToday()
                                        ? $conversation->last_message->created_at->format('H:i')
                                        : $conversation->last_message->created_at->format('d/m');
                                @endphp
                                <a href="{{ route('messages.conversation', $conversation->user) }}" class="group flex items-center gap-4 px-5 py-4 transition hover:bg-stone-50/90 sm:px-6 {{ $conversation->unread_count > 0 ? 'bg-[rgba(79,70,229,0.04)]' : 'bg-white' }}">
                                    <div class="relative shrink-0">
                                        <x-user-avatar :user="$conversation->user" class="h-14 w-14 bg-[var(--brand)] text-sm font-semibold uppercase text-white shadow-[0_10px_24px_rgba(79,70,229,0.18)]" />
                                        @if ($conversation->unread_count > 0)
                                            <span class="absolute -right-1 -top-1 inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-[var(--brand)] px-1.5 text-[0.68rem] font-semibold text-white">
                                                {{ $conversation->unread_count }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <p class="truncate text-base font-semibold text-stone-950">
                                                    {{ $conversation->user->name }}
                                                </p>
                                                <p class="mt-1 truncate text-sm text-stone-500">
                                                    {{ $conversation->last_message->sender_id === auth()->id() ? 'Vous : ' : '' }}{{ $lastMessagePreview }}
                                                </p>
                                            </div>
                                            <div class="shrink-0 text-right">
                                                <p class="text-xs font-medium text-stone-400">{{ $lastMessageTime }}</p>
                                                @if ($conversation->unread_count > 0)
                                                    <span class="mt-2 inline-flex h-2.5 w-2.5 rounded-full bg-[var(--brand)]"></span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="px-6 py-16 text-center">
                                    <p class="section-kicker">{{ $search !== '' || $unreadOnly ? 'Aucun resultat' : 'Aucun message' }}</p>
                                    <h3 class="mt-3 text-3xl font-semibold text-stone-950">
                                        {{ $search !== '' || $unreadOnly ? 'Aucune conversation ne correspond a ces criteres' : 'Ta messagerie est vide' }}
                                    </h3>
                                    @if ($search !== '' || $unreadOnly)
                                        <p class="mt-3 text-sm text-stone-500">
                                            Ajuste les filtres ou reviens a l'affichage complet.
                                        </p>
                                    @endif
                                </div>
                            @endforelse
                        </div>

                        <div class="border-t border-[rgba(15,23,42,0.08)] px-4 py-4 sm:px-6">
                            {{ $conversations->links() }}
                        </div>
                    </div>

                    <aside class="bg-[linear-gradient(180deg,rgba(248,250,252,0.92),rgba(255,255,255,0.98))] p-5 sm:p-6">
                        <div class="space-y-5">
                            <div>
                                <p class="section-kicker">Activite recente</p>
                                <h3 class="mt-2 text-2xl font-semibold text-stone-950">Notifications</h3>
                                <p class="mt-3 text-sm leading-6 text-stone-500">
                                    Les nouvelles notifications sont marquees comme lues a l'ouverture de cette boite.
                                </p>
                            </div>

                            <div class="rounded-[1.8rem] border border-[rgba(15,23,42,0.08)] bg-white px-5 py-5">
                                <p class="text-[0.68rem] font-semibold uppercase tracking-[0.18em] text-stone-400">Vue d'ensemble</p>
                                <div class="mt-4 flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-3xl font-semibold text-stone-950">{{ $conversationSummary['total'] }}</p>
                                        <p class="mt-1 text-sm text-stone-500">conversation(s)</p>
                                    </div>
                                    <div class="h-12 w-px bg-[rgba(15,23,42,0.08)]"></div>
                                    <div class="text-right">
                                        <p class="text-3xl font-semibold text-stone-950">{{ $conversationSummary['unread'] }}</p>
                                        <p class="mt-1 text-sm text-stone-500">avec du nouveau</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                @forelse ($notifications as $notification)
                                    @php
                                        $notificationType = $notification->data['type'] ?? 'notification';
                                        $notificationLabel = match ($notificationType) {
                                            'new_private_message' => 'Message prive',
                                            'follow_request' => 'Demande de suivi',
                                            'new_topic_followed_tag' => 'Sujet recommande',
                                            'reply_reported' => 'Signalement',
                                            default => array_key_exists('warning_count', $notification->data) ? 'Moderation' : 'Nouvelle reponse',
                                        };
                                        $notificationUrl = $notification->data['url'] ?? null;
                                    @endphp

                                    <article class="rounded-[1.6rem] border border-[rgba(15,23,42,0.08)] bg-white px-4 py-4 shadow-[0_12px_28px_rgba(15,23,42,0.04)]">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <span class="rounded-full bg-stone-100 px-3 py-1 text-[0.68rem] font-semibold uppercase tracking-[0.18em] text-stone-700">
                                                {{ $notificationLabel }}
                                            </span>
                                            <span class="text-[0.72rem] text-stone-400">{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div class="mt-3">
                                            <p class="text-sm font-semibold text-stone-950">
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                            </p>
                                            <p class="mt-1 text-sm leading-6 text-stone-500">
                                                {{ $notification->data['message'] ?? '' }}
                                            </p>
                                        </div>
                                        @if (array_key_exists('warning_count', $notification->data))
                                            <p class="mt-3 text-sm font-semibold text-stone-900">
                                                {{ $notification->data['warning_count'] }} avertissement(s)
                                            </p>
                                        @endif
                                        @if (($notification->data['type'] ?? null) === 'new_reply')
                                            <div class="flex flex-wrap items-center gap-3 text-sm text-stone-600">
                                                @if (! empty($notification->data['reply_user_url']))
                                                    <a href="{{ $notification->data['reply_user_url'] }}" class="rounded-full bg-[rgba(79,70,229,0.12)] px-3 py-1 font-semibold text-[var(--brand)] transition hover:bg-[rgba(79,70,229,0.18)]">
                                                        {{ $notification->data['reply_user'] ?? 'Un membre' }}
                                                    </a>
                                                @endif
                                                @if (! empty($notification->data['topic_title']))
                                                    <span class="font-semibold text-stone-900">{{ $notification->data['topic_title'] }}</span>
                                                @endif
                                            </div>
                                        @elseif (($notification->data['type'] ?? null) === 'new_private_message')
                                            <div class="flex flex-wrap items-center gap-3 text-sm text-stone-600">
                                                @if (! empty($notification->data['sender_url']))
                                                    <a href="{{ $notification->data['sender_url'] }}" class="rounded-full bg-[rgba(139,92,246,0.12)] px-3 py-1 font-semibold text-[var(--brand)] transition hover:bg-[rgba(139,92,246,0.18)]">
                                                        {{ $notification->data['sender_name'] ?? 'Un membre' }}
                                                    </a>
                                                @endif
                                            </div>
                                        @elseif (($notification->data['type'] ?? null) === 'follow_request')
                                            <div class="flex flex-wrap items-center gap-3 text-sm text-stone-600">
                                                @if (! empty($notification->data['requester_url']))
                                                    <a href="{{ $notification->data['requester_url'] }}" class="rounded-full bg-[rgba(139,92,246,0.12)] px-3 py-1 font-semibold text-[var(--brand)] transition hover:bg-[rgba(139,92,246,0.18)]">
                                                        {{ $notification->data['requester_name'] ?? 'Un membre' }}
                                                    </a>
                                                @endif
                                            </div>
                                        @elseif (($notification->data['type'] ?? null) === 'new_topic_followed_tag')
                                            <div class="flex flex-wrap items-center gap-3 text-sm text-stone-600">
                                                @if (! empty($notification->data['topic_title']))
                                                    <span class="font-semibold text-stone-900">{{ $notification->data['topic_title'] }}</span>
                                                @endif
                                                @if (! empty($notification->data['tag_name']))
                                                    <span class="rounded-full bg-amber-100 px-3 py-1 font-semibold text-amber-700">
                                                        {{ $notification->data['tag_name'] }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                        @if ($notificationUrl)
                                            <div class="mt-4">
                                                <a href="{{ $notificationUrl }}" class="inline-flex rounded-full bg-stone-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-stone-800">
                                                    Ouvrir
                                                </a>
                                            </div>
                                        @endif
                                    </article>
                                @empty
                                    <div class="rounded-[1.6rem] border border-[rgba(15,23,42,0.08)] bg-white px-5 py-6 text-sm text-stone-500">
                                        Rien a signaler pour le moment.
                                    </div>
                                @endforelse
                            </div>

                            <div class="rounded-[1.8rem] border border-[rgba(15,23,42,0.08)] bg-white px-5 py-5">
                                <p class="text-[0.68rem] font-semibold uppercase tracking-[0.18em] text-stone-400">Conseil</p>
                                <p class="mt-3 text-sm leading-6 text-stone-600">
                                    Une conversation est plus lisible quand le premier message annonce directement le contexte ou la demande.
                                </p>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
