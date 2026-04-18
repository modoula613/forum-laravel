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
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="glass-panel rounded-[2rem] p-5 sm:p-6">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-[1.5rem] bg-white/80 px-5 py-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-400">Messages non lus</p>
                        <p class="mt-3 text-3xl font-semibold text-stone-950">{{ $inboxSummary['messages_unread'] }}</p>
                    </div>
                    <div class="rounded-[1.5rem] bg-white/80 px-5 py-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-400">Notifications recentes</p>
                        <p class="mt-3 text-3xl font-semibold text-stone-950">{{ $inboxSummary['notifications_total'] }}</p>
                    </div>
                    <div class="rounded-[1.5rem] bg-[rgba(79,70,229,0.08)] px-5 py-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--brand)]">A traiter</p>
                        <p class="mt-3 text-3xl font-semibold text-stone-950">{{ $inboxSummary['messages_unread'] + $inboxSummary['notifications_unread'] }}</p>
                    </div>
                </div>
            </section>

            <section class="glass-panel rounded-[2rem] p-5 sm:p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="section-kicker">Messages prives</p>
                        <h3 class="mt-2 text-2xl font-semibold text-stone-950">Conversations</h3>
                    </div>
                    <form method="GET" action="{{ route('messages.index') }}" class="flex flex-col gap-4 lg:flex-row lg:items-end">
                        <div class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_auto] lg:w-full lg:min-w-[34rem]">
                            <div>
                                <label for="search" class="mb-2 block text-sm font-semibold uppercase tracking-[0.16em] text-stone-500">
                                    Rechercher
                                </label>
                                <input
                                    id="search"
                                    type="text"
                                    name="search"
                                    value="{{ $search }}"
                                    placeholder="Nom du membre ou contenu du dernier message"
                                    class="block w-full rounded-[1.5rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                                >
                            </div>
                            <label class="inline-flex items-center gap-3 rounded-[1.5rem] border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-3 text-sm font-semibold text-stone-700">
                                <input
                                    type="checkbox"
                                    name="unread"
                                    value="1"
                                    @checked($unreadOnly)
                                    class="rounded border-stone-300 text-[var(--brand)] shadow-sm focus:ring-[var(--brand)]"
                                >
                                Seulement les non lus
                            </label>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            @if ($search !== '' || $unreadOnly)
                                <a href="{{ route('messages.index') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-white">
                                    Reinitialiser
                                </a>
                            @endif
                            <x-primary-button>Filtrer</x-primary-button>
                        </div>
                    </form>
                </div>

                <div class="mt-5 flex flex-wrap gap-3 text-sm text-stone-600">
                    <span class="rounded-full bg-white/80 px-4 py-2 font-medium text-stone-700">
                        {{ $conversationSummary['displayed'] ?? $conversations->total() }} conversation(s) affichee(s)
                    </span>
                    <span class="rounded-full bg-[rgba(79,70,229,0.12)] px-4 py-2 font-medium text-[var(--brand)]">
                        {{ $conversationSummary['unread'] }} avec des messages non lus
                    </span>
                </div>
            </section>

            @if (session('success'))
                <div class="glass-panel rounded-[1.5rem] border-emerald-200 bg-emerald-50/90 px-5 py-4 text-sm text-emerald-900">
                    {{ session('success') }}
                </div>
            @endif

            <section class="space-y-4">
                @forelse ($conversations as $conversation)
                    <article class="glass-panel rounded-[2rem] p-6">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="flex items-start gap-4">
                                <x-user-avatar :user="$conversation->user" class="mt-1 h-12 w-12 shrink-0 bg-[var(--brand)] text-sm font-semibold uppercase text-white shadow-[0_10px_24px_rgba(79,70,229,0.22)]" />
                                <div>
                                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-stone-500">Utilisateur concerne</p>
                                    <h3 class="mt-2 text-2xl font-semibold text-stone-950">
                                        <x-user-link :user="$conversation->user">
                                            {{ $conversation->user->name }}
                                        </x-user-link>
                                    </h3>
                                    <p class="mt-4 whitespace-pre-line text-base leading-8 text-stone-700">{{ $conversation->last_message->content }}</p>
                                </div>
                            </div>
                            <div class="text-right text-sm text-stone-500">
                                <p>{{ $conversation->last_message->created_at->format('d/m/Y H:i') }}</p>
                                @if ($conversation->unread_count > 0)
                                    <div class="mt-3">
                                        <span class="inline-flex rounded-full bg-[rgba(139,92,246,0.12)] px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-[var(--brand)]">
                                            Nouveau
                                        </span>
                                    </div>
                                @endif
                                <span class="mt-3 inline-flex rounded-full {{ $conversation->unread_count > 0 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }} px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em]">
                                    {{ $conversation->unread_count > 0 ? $conversation->unread_count.' non lu(s)' : 'A jour' }}
                                </span>
                                <a href="{{ route('messages.conversation', $conversation->user) }}" class="mt-3 inline-flex rounded-full bg-[var(--brand)] px-4 py-2 font-semibold text-white transition hover:bg-[var(--brand-deep)]">
                                    Ouvrir conversation
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="glass-panel rounded-[2rem] border-dashed p-12 text-center">
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
            </section>

            <section class="glass-panel rounded-[2rem] p-5 sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="section-kicker">Activite recente</p>
                        <h3 class="mt-2 text-2xl font-semibold text-stone-950">Notifications</h3>
                    </div>
                    <p class="text-sm text-stone-500">
                        Les nouvelles notifications sont marquees comme lues a l'ouverture de cette boite.
                    </p>
                </div>

                <div class="mt-5 space-y-4">
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

                        <article class="rounded-[1.6rem] bg-white/70 p-5">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <span class="rounded-full bg-[rgba(79,70,229,0.12)] px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-[var(--brand)]">
                                            {{ $notificationLabel }}
                                        </span>
                                        <span class="text-xs text-stone-500">{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <h4 class="mt-3 text-xl font-semibold text-stone-950">
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                    </h4>
                                    <p class="mt-2 text-sm leading-7 text-stone-600">
                                        {{ $notification->data['message'] ?? '' }}
                                    </p>
                                    @if (array_key_exists('warning_count', $notification->data))
                                        <p class="mt-3 text-sm font-semibold text-stone-900">
                                            {{ $notification->data['warning_count'] }} avertissement(s)
                                        </p>
                                    @endif
                                    @if (($notification->data['type'] ?? null) === 'new_reply')
                                        <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-stone-600">
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
                                        <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-stone-600">
                                            @if (! empty($notification->data['sender_url']))
                                                <a href="{{ $notification->data['sender_url'] }}" class="rounded-full bg-[rgba(139,92,246,0.12)] px-3 py-1 font-semibold text-[var(--brand)] transition hover:bg-[rgba(139,92,246,0.18)]">
                                                    {{ $notification->data['sender_name'] ?? 'Un membre' }}
                                                </a>
                                            @endif
                                        </div>
                                    @elseif (($notification->data['type'] ?? null) === 'follow_request')
                                        <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-stone-600">
                                            @if (! empty($notification->data['requester_url']))
                                                <a href="{{ $notification->data['requester_url'] }}" class="rounded-full bg-[rgba(139,92,246,0.12)] px-3 py-1 font-semibold text-[var(--brand)] transition hover:bg-[rgba(139,92,246,0.18)]">
                                                    {{ $notification->data['requester_name'] ?? 'Un membre' }}
                                                </a>
                                            @endif
                                        </div>
                                    @elseif (($notification->data['type'] ?? null) === 'new_topic_followed_tag')
                                        <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-stone-600">
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
                                </div>
                                @if ($notificationUrl)
                                    <a href="{{ $notificationUrl }}" class="inline-flex rounded-full bg-[var(--brand)] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[var(--brand-deep)]">
                                        Ouvrir
                                    </a>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="rounded-[1.6rem] bg-white/70 p-6 text-sm text-stone-500">
                            Rien a signaler pour le moment.
                        </div>
                    @endforelse
                </div>
            </section>

            <div class="glass-panel rounded-[2rem] px-4 py-4 sm:px-6">
                {{ $conversations->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
