<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl">
                <p class="section-kicker">Conversation</p>
                <h2 class="mt-2 text-2xl font-semibold text-stone-950">
                    Discussion avec
                    <x-user-link :user="$user">
                        {{ $user->name }}
                    </x-user-link>
                </h2>
            </div>
            <a href="{{ route('messages.index') }}" class="inline-flex items-center rounded-full border border-[rgba(71,85,135,0.14)] bg-white/80 px-4 py-2.5 text-sm font-semibold text-stone-700 transition hover:bg-white">
                Retour a la boite
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-[1040px] space-y-5 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="glass-panel rounded-[1.5rem] border-emerald-200 bg-emerald-50/90 px-5 py-4 text-sm text-emerald-900">
                    {{ session('success') }}
                </div>
            @endif

            <section class="overflow-hidden rounded-[2rem] border border-white/8 bg-[#111318] text-white shadow-[0_24px_60px_rgba(2,6,23,0.22)]">
                <div class="border-b border-white/8 bg-[#12151b] px-4 py-4 sm:px-6">
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('messages.index') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white/75 transition hover:bg-white/10 hover:text-white" aria-label="Retour a la boite">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill="currentColor" d="m14.7 6.3 1.4 1.4L11.8 12l4.3 4.3-1.4 1.4L9 12l5.7-5.7Z"/>
                                </svg>
                            </a>
                            <x-user-avatar :user="$user" class="h-12 w-12 shrink-0 bg-[#2a2f39] text-sm font-semibold uppercase text-white" />
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-white/38">Discussion avec</p>
                                <h3 class="truncate text-lg font-semibold text-white">{{ $user->name }}</h3>
                                <p class="text-xs text-white/32">{{ $messages->count() }} message(s)</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('users.show', $user) }}" class="inline-flex rounded-full border border-white/8 bg-white/[0.05] px-4 py-2 text-sm font-semibold text-white/82 transition hover:bg-white/[0.08] hover:text-white">
                                    Voir profil
                                </a>
                            </div>
                        </div>

                        <form method="GET" action="{{ route('messages.conversation', $user) }}">
                            <div class="rounded-[1.5rem] border border-white/8 bg-white/[0.03] p-2">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                    <div class="relative min-w-0 flex-1">
                                        <label for="search" class="sr-only">Rechercher dans la conversation</label>
                                        <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/30" viewBox="0 0 24 24" aria-hidden="true">
                                            <path fill="currentColor" d="M10.5 4a6.5 6.5 0 1 0 4.03 11.6l4.44 4.43 1.06-1.06-4.43-4.44A6.5 6.5 0 0 0 10.5 4Zm0 1.5a5 5 0 1 1 0 10 5 5 0 0 1 0-10Z"/>
                                        </svg>
                                        <input
                                            id="search"
                                            type="text"
                                            name="search"
                                            value="{{ request('search') }}"
                                            placeholder="Rechercher"
                                            class="block w-full rounded-full border border-transparent bg-white/[0.05] py-2.5 pl-11 pr-4 text-sm text-white placeholder:text-white/30 focus:border-white/12 focus:bg-white/[0.07] focus:ring-0"
                                        >
                                    </div>
                                    <div class="flex items-center gap-2 sm:shrink-0">
                                        <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/8 bg-white/[0.05] text-white/70 transition hover:bg-white/[0.08] hover:text-white" aria-label="Rechercher">
                                            <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" aria-hidden="true">
                                                <path fill="currentColor" d="M10.5 4a6.5 6.5 0 1 0 4.03 11.6l4.44 4.43 1.06-1.06-4.43-4.44A6.5 6.5 0 0 0 10.5 4Zm0 1.5a5 5 0 1 1 0 10 5 5 0 0 1 0-10Z"/>
                                            </svg>
                                        </button>
                                        @if (request()->filled('search'))
                                            <a href="{{ route('messages.conversation', $user) }}" class="inline-flex h-10 items-center rounded-full border border-white/8 px-3 text-sm font-semibold text-white/58 transition hover:bg-white/[0.06] hover:text-white">
                                                Reset
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="max-h-[70vh] overflow-y-auto bg-[#12151b] px-4 py-5 sm:px-6">
                    <div class="mx-auto max-w-[760px] space-y-3">
                        @forelse ($messages as $message)
                            @php
                                $isCurrentUser = $message->sender_id === auth()->id();
                                $previousMessage = $loop->index > 0 ? $messages[$loop->index - 1] : null;
                                $showDateSeparator = $loop->first || optional($previousMessage?->created_at)->toDateString() !== $message->created_at->toDateString();
                            @endphp
                            @if ($showDateSeparator)
                                <div class="flex justify-center py-2">
                                    <span class="rounded-full bg-white/6 px-3 py-1 text-[11px] font-medium text-white/38">
                                        {{ $message->created_at->format('d M Y, H:i') }}
                                    </span>
                                </div>
                            @endif
                            <article class="flex {{ $isCurrentUser ? 'justify-end' : 'justify-start' }}">
                                <div class="relative max-w-[82%] sm:max-w-[70%]">
                                    <div class="pointer-events-none absolute inset-y-2 {{ $isCurrentUser ? 'right-0' : 'left-0' }} w-16 rounded-full {{ $isCurrentUser ? 'bg-[radial-gradient(circle,rgba(255,255,255,0.12)_0%,rgba(255,255,255,0.03)_46%,rgba(255,255,255,0)_76%)]' : 'bg-[radial-gradient(circle,rgba(255,255,255,0.08)_0%,rgba(255,255,255,0.02)_48%,rgba(255,255,255,0)_78%)]' }}"></div>
                                    <div class="relative {{ $isCurrentUser ? 'ml-auto rounded-[2rem_2rem_0.55rem_2rem] border border-white/8 bg-[#242a33] text-white' : 'rounded-[2rem_2rem_2rem_0.55rem] border border-white/8 bg-[#1a1d24] text-white/92' }} px-4 py-3 shadow-[0_14px_28px_rgba(2,6,23,0.16)]">
                                        <p class="whitespace-pre-line text-[0.95rem] leading-6">{{ $message->content }}</p>
                                    </div>

                                    <div class="mt-1 flex items-center gap-3 text-[11px] text-white/32 {{ $isCurrentUser ? 'justify-end pr-1' : 'justify-start pl-1' }}">
                                        <span>{{ $message->created_at->format('H:i') }}</span>
                                        <form method="POST" action="{{ route('messages.destroy', $message) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-medium text-white/32 transition hover:text-rose-400">
                                                Suppr.
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="mx-auto max-w-xl rounded-[2.1rem] border border-dashed border-white/8 bg-[#171a22] px-8 py-14 text-center">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/35">Aucun message</p>
                                <h3 class="mt-3 text-3xl font-semibold text-white">La conversation n'a pas encore commence</h3>
                                <p class="mt-3 text-sm leading-6 text-white/42">Envoyez le premier message pour lancer l'echange.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="border-t border-white/8 bg-[#12151b] px-4 py-4 sm:px-6">
                    <form method="POST" action="{{ route('messages.send') }}" class="mx-auto max-w-[760px]">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                        <div class="flex items-end gap-3 rounded-[2.1rem] border border-white/8 bg-[#171a22] px-3 py-3 shadow-[0_14px_28px_rgba(2,6,23,0.14)]">
                            <label for="content" class="inline-flex h-10 w-10 cursor-text items-center justify-center rounded-full bg-white/[0.05] text-white/62 transition hover:bg-white/[0.08] hover:text-white" aria-label="Ecrire un message">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill="currentColor" d="M12 2a10 10 0 1 1 0 20 10 10 0 0 1 0-20Zm0 1.5a8.5 8.5 0 1 0 0 17 8.5 8.5 0 0 0 0-17Zm-3 5.75a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5Zm6 0a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5Zm1.2 5.22.9.66A6.3 6.3 0 0 1 12 17.8a6.3 6.3 0 0 1-5.1-2.67l.9-.66A5.17 5.17 0 0 0 12 16.3a5.17 5.17 0 0 0 4.2-1.83Z"/>
                                </svg>
                            </label>
                            <div class="flex-1">
                                <label for="content" class="sr-only">Ecris ton message</label>
                                <textarea
                                    id="content"
                                    name="content"
                                    rows="1"
                                    placeholder="Ecris un message..."
                                    class="block min-h-[44px] w-full resize-none border-0 bg-transparent px-2 py-2 text-sm text-white placeholder:text-white/35 focus:ring-0"
                                    required
                                >{{ old('content') }}</textarea>
                            </div>
                            <button type="submit" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/8 bg-white/[0.05] text-white/72 transition hover:bg-white/[0.08] hover:text-white" aria-label="Envoyer">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill="currentColor" d="m3.4 20.4 17.85-7.65c.5-.21.5-.93 0-1.14L3.4 3.96c-.46-.2-.94.2-.84.7l1.58 7.05a.75.75 0 0 0 .58.57l7.13 1.43-7.13 1.43a.75.75 0 0 0-.58.57l-1.58 7.05c-.1.5.38.9.84.7Z"/>
                                </svg>
                            </button>
                        </div>
                        <x-input-error class="mt-3" :messages="$errors->get('content')" />
                    </form>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
