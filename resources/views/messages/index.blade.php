<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl">
                <p class="section-kicker">Boite</p>
                <h2 class="mt-2 text-2xl font-semibold text-stone-950">Boite de reception</h2>
            </div>
            <a href="{{ route('users.index') }}" class="inline-flex items-center rounded-full border border-[rgba(71,85,135,0.14)] bg-white/80 px-4 py-2.5 text-sm font-semibold text-stone-700 transition hover:bg-white">
                Chercher un membre
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-[1240px] space-y-5 px-4 sm:px-6 lg:px-8">
            @php
                $currentUser = auth()->user();
            @endphp

            @if (session('success'))
                <div class="glass-panel rounded-[1.5rem] border-emerald-200 bg-emerald-50/90 px-5 py-4 text-sm text-emerald-900">
                    {{ session('success') }}
                </div>
            @endif

            <section class="overflow-hidden rounded-[2rem] border border-white/8 bg-[#111318] text-white shadow-[0_24px_60px_rgba(2,6,23,0.22)]">
                <div class="grid min-h-[72vh] gap-0 xl:grid-cols-[380px_minmax(0,1fr)]">
                    <aside class="border-b border-white/8 bg-[#12151b] xl:border-b-0 xl:border-r">
                        <div class="border-b border-white/8 px-5 py-4 sm:px-6">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex min-w-0 items-center gap-3">
                                    <x-user-avatar :user="$currentUser" class="h-11 w-11 shrink-0 bg-[#2a2f39] text-sm font-semibold uppercase text-white" />
                                    <div class="min-w-0">
                                        <p class="truncate text-base font-semibold text-white">{{ $currentUser->name }}</p>
                                        <p class="text-xs text-white/42">Messages prives</p>
                                    </div>
                                </div>
                                <a href="{{ route('users.index') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white/72 transition hover:bg-white/10 hover:text-white" aria-label="Chercher un membre">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill="currentColor" d="M10.5 4a6.5 6.5 0 1 0 4.03 11.6l4.44 4.43 1.06-1.06-4.43-4.44A6.5 6.5 0 0 0 10.5 4Zm0 1.5a5 5 0 1 1 0 10 5 5 0 0 1 0-10Z"/>
                                    </svg>
                                </a>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <div class="rounded-full border border-white/8 bg-white/[0.04] px-3 py-1.5 text-xs font-semibold text-white/68">
                                    {{ $inboxSummary['messages_unread'] }} non lu(s)
                                </div>
                                <div class="rounded-full border border-white/8 bg-white/[0.04] px-3 py-1.5 text-xs font-semibold text-white/56">
                                    {{ $inboxSummary['notifications_total'] }} notification(s)
                                </div>
                                <div class="rounded-full border border-white/8 bg-[#1b1f27] px-3 py-1.5 text-xs font-semibold text-white/72">
                                    {{ $inboxSummary['messages_unread'] + $inboxSummary['notifications_unread'] }} a traiter
                                </div>
                            </div>

                            <form method="GET" action="{{ route('messages.index') }}" class="mt-4">
                                <div class="rounded-[1.5rem] border border-white/8 bg-white/[0.03] p-2">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                        <div class="relative min-w-0 flex-1">
                                            <label for="search" class="sr-only">Rechercher</label>
                                            <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/30" viewBox="0 0 24 24" aria-hidden="true">
                                                <path fill="currentColor" d="M10.5 4a6.5 6.5 0 1 0 4.03 11.6l4.44 4.43 1.06-1.06-4.43-4.44A6.5 6.5 0 0 0 10.5 4Zm0 1.5a5 5 0 1 1 0 10 5 5 0 0 1 0-10Z"/>
                                            </svg>
                                            <input
                                                id="search"
                                                type="text"
                                                name="search"
                                                value="{{ $search }}"
                                                placeholder="Rechercher"
                                                class="block w-full rounded-full border border-transparent bg-white/[0.05] py-2.5 pl-11 pr-4 text-sm text-white placeholder:text-white/30 focus:border-white/12 focus:bg-white/[0.07] focus:ring-0"
                                            >
                                        </div>
                                        <div class="flex items-center gap-2 sm:shrink-0">
                                            <label class="inline-flex items-center justify-center gap-2 rounded-full border border-white/8 bg-white/[0.05] px-3.5 py-2.5 text-sm font-semibold text-white/68">
                                                <input
                                                    type="checkbox"
                                                    name="unread"
                                                    value="1"
                                                    @checked($unreadOnly)
                                                    class="rounded border-white/20 bg-transparent text-white/80 shadow-sm focus:ring-0"
                                                >
                                                Non lus
                                            </label>
                                            <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/8 bg-white/[0.05] text-white/70 transition hover:bg-white/[0.08] hover:text-white" aria-label="Appliquer les filtres">
                                                <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path fill="currentColor" d="M10.5 4a6.5 6.5 0 1 0 4.03 11.6l4.44 4.43 1.06-1.06-4.43-4.44A6.5 6.5 0 0 0 10.5 4Zm0 1.5a5 5 0 1 1 0 10 5 5 0 0 1 0-10Z"/>
                                                </svg>
                                            </button>
                                            @if ($search !== '' || $unreadOnly)
                                                <a href="{{ route('messages.index') }}" class="inline-flex h-10 items-center rounded-full border border-white/8 px-3 text-sm font-semibold text-white/55 transition hover:bg-white/[0.06] hover:text-white">
                                                    Reset
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-2 px-2 text-[11px] font-medium text-white/38">
                                        {{ $conversationSummary['displayed'] ?? $conversations->total() }} conversation(s) affichee(s)
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="max-h-[calc(72vh-170px)] overflow-y-auto px-2 py-2">
                            @forelse ($conversations as $conversation)
                                @php
                                    $lastMessagePreview = \Illuminate\Support\Str::limit($conversation->last_message->content, 74);
                                    $lastMessageTime = $conversation->last_message->created_at->isToday()
                                        ? $conversation->last_message->created_at->format('H:i')
                                        : $conversation->last_message->created_at->format('d/m');
                                @endphp
                                <a href="{{ route('messages.conversation', $conversation->user) }}" class="group mb-1.5 flex items-center gap-3 rounded-[1.7rem] border border-transparent px-4 py-3 transition {{ $conversation->unread_count > 0 ? 'bg-[#171b24] hover:bg-[#1c212c] hover:border-white/8' : 'hover:bg-white/[0.04] hover:border-white/6' }}">
                                    <div class="relative shrink-0">
                                        <x-user-avatar :user="$conversation->user" class="h-12 w-12 bg-[#2a2f39] text-sm font-semibold uppercase text-white" />
                                        @if ($conversation->unread_count > 0)
                                            <span class="absolute -right-1 -top-1 inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-white text-[0.68rem] font-semibold text-[#111318]">
                                                {{ $conversation->unread_count }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-semibold text-white">
                                                    {{ $conversation->user->name }}
                                                </p>
                                                <p class="mt-1 truncate text-sm text-white/44">
                                                    {{ $conversation->last_message->sender_id === auth()->id() ? 'Vous : ' : '' }}{{ $lastMessagePreview }}
                                                </p>
                                            </div>
                                            <div class="shrink-0 text-right">
                                                <p class="text-[11px] font-medium text-white/32">{{ $lastMessageTime }}</p>
                                                @if ($conversation->unread_count > 0)
                                                    <span class="mt-2 inline-flex h-2.5 w-2.5 rounded-full bg-[#5b61ff]"></span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="px-6 py-16 text-center">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/35">{{ $search !== '' || $unreadOnly ? 'Aucun resultat' : 'Aucun message' }}</p>
                                    <h3 class="mt-3 text-2xl font-semibold text-white">
                                        {{ $search !== '' || $unreadOnly ? 'Aucune conversation ne correspond a ces criteres' : 'Ta messagerie est vide' }}
                                    </h3>
                                    @if ($search !== '' || $unreadOnly)
                                        <p class="mt-3 text-sm text-white/45">
                                            Ajuste les filtres ou reviens a l'affichage complet.
                                        </p>
                                    @endif
                                </div>
                            @endforelse
                        </div>

                        <div class="border-t border-white/10 px-4 py-4 sm:px-6">
                            {{ $conversations->links() }}
                        </div>
                    </aside>

                    <div class="flex min-h-full flex-col bg-[#14171d]">
                        <div class="border-b border-white/8 px-6 py-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-white/35">Activite recente</p>
                            <h3 class="mt-2 text-xl font-semibold text-white">Notifications</h3>
                        </div>

                        <div class="flex flex-1 flex-col justify-between p-6">
                            <div class="space-y-3">
                                    @forelse ($notifications as $notification)
                                        @php
                                            $notificationType = $notification->data['type'] ?? 'notification';
                                            $notificationLabel = match ($notificationType) {
                                                'new_private_message' => 'Message prive',
                                                'follow_request' => 'Demande de suivi',
                                                'reply_reported' => 'Signalement',
                                                default => array_key_exists('warning_count', $notification->data) ? 'Moderation' : 'Nouvelle reponse',
                                            };
                                            $notificationUrl = $notification->data['url'] ?? null;
                                        @endphp

                                        <article class="rounded-[1.7rem] border border-white/8 bg-[#171a22] px-4 py-4">
                                            <div class="flex flex-wrap items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-white/32">
                                                <span>{{ $notificationLabel }}</span>
                                                <span>{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="mt-2">
                                                <p class="text-sm font-semibold text-white">
                                                    {{ $notification->data['title'] ?? 'Notification' }}
                                                </p>
                                                <p class="mt-1 text-sm leading-6 text-white/45">
                                                    {{ $notification->data['message'] ?? '' }}
                                                </p>
                                            </div>
                                            @if (($notification->data['type'] ?? null) === 'new_reply')
                                                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-white/48">
                                                    @if (! empty($notification->data['reply_user_url']))
                                                        <a href="{{ $notification->data['reply_user_url'] }}" class="rounded-full bg-white/[0.06] px-3 py-1 font-semibold text-white/80 transition hover:bg-white/[0.1] hover:text-white">
                                                            {{ $notification->data['reply_user'] ?? 'Un membre' }}
                                                        </a>
                                                    @endif
                                                    @if (! empty($notification->data['topic_title']))
                                                        <span class="font-semibold text-white">{{ $notification->data['topic_title'] }}</span>
                                                    @endif
                                                </div>
                                            @elseif (($notification->data['type'] ?? null) === 'new_private_message')
                                                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-white/48">
                                                    @if (! empty($notification->data['sender_url']))
                                                        <a href="{{ $notification->data['sender_url'] }}" class="rounded-full bg-white/[0.06] px-3 py-1 font-semibold text-white/80 transition hover:bg-white/[0.1] hover:text-white">
                                                            {{ $notification->data['sender_name'] ?? 'Un membre' }}
                                                        </a>
                                                    @endif
                                                </div>
                                            @elseif (($notification->data['type'] ?? null) === 'follow_request')
                                                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-white/48">
                                                    @if (! empty($notification->data['requester_url']))
                                                        <a href="{{ $notification->data['requester_url'] }}" class="rounded-full bg-white/[0.06] px-3 py-1 font-semibold text-white/80 transition hover:bg-white/[0.1] hover:text-white">
                                                            {{ $notification->data['requester_name'] ?? 'Un membre' }}
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                                            @if (array_key_exists('warning_count', $notification->data))
                                                <p class="mt-3 text-sm font-semibold text-white">
                                                    {{ $notification->data['warning_count'] }} avertissement(s)
                                                </p>
                                            @endif
                                            @if ($notificationUrl)
                                                <div class="mt-3">
                                                    <a href="{{ $notificationUrl }}" class="inline-flex rounded-full border border-white/8 bg-white/[0.05] px-4 py-2 text-sm font-semibold text-white/78 transition hover:bg-white/[0.08] hover:text-white">
                                                        Ouvrir
                                                    </a>
                                                </div>
                                            @endif
                                        </article>
                                    @empty
                                        <div class="rounded-[1.6rem] border border-white/8 bg-[#171a22] px-5 py-6 text-sm text-white/45">
                                            Rien a signaler pour le moment.
                                        </div>
                                    @endforelse
                            </div>
                            <div class="mt-6 rounded-[1.7rem] border border-white/8 bg-[#171a22] px-5 py-4 text-sm text-white/45">
                                Une conversation est plus lisible quand le premier message annonce directement le contexte ou la demande.
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
