<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="section-kicker">Conversation</p>
                <h2 class="mt-3 text-4xl font-semibold text-stone-950">
                    Discussion avec
                    <x-user-link :user="$user">
                        {{ $user->name }}
                    </x-user-link>
                </h2>
                <p class="muted-copy mt-3 text-base leading-7">
                    Echange prive entre membres. Les messages les plus anciens apparaissent en premier.
                </p>
            </div>
            <a href="{{ route('messages.index') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                Retour a la boite
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="glass-panel rounded-[1.5rem] border-emerald-200 bg-emerald-50/90 px-5 py-4 text-sm text-emerald-900">
                    {{ session('success') }}
                </div>
            @endif

            <section class="overflow-hidden rounded-[2.6rem] border border-[rgba(15,23,42,0.08)] bg-white/96 shadow-[0_28px_80px_rgba(15,23,42,0.08)]">
                <div class="border-b border-[rgba(15,23,42,0.08)] px-5 py-5 sm:px-6">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex items-center gap-4">
                            <x-user-avatar :user="$user" class="h-14 w-14 shrink-0 bg-[var(--brand)] text-base font-semibold uppercase text-white shadow-[0_10px_24px_rgba(79,70,229,0.22)]" />
                            <div>
                                <p class="section-kicker">Messagerie privee</p>
                                <h3 class="mt-2 text-2xl font-semibold text-stone-950">{{ $user->name }}</h3>
                                <p class="mt-2 text-sm leading-6 text-stone-500">
                                    {{ $messages->count() }} message(s) dans cette conversation
                                </p>
                            </div>
                        </div>
                        <div class="flex w-full flex-col gap-3 lg:w-auto lg:min-w-[28rem]">
                            <form method="GET" action="{{ route('messages.conversation', $user) }}" class="flex flex-col gap-3 sm:flex-row">
                                <div class="flex-1">
                                    <label for="search" class="sr-only">Rechercher dans la conversation</label>
                                    <input
                                        id="search"
                                        type="text"
                                        name="search"
                                        value="{{ request('search') }}"
                                        placeholder="Rechercher un mot ou une phrase dans cet echange"
                                        class="block w-full rounded-full border border-[rgba(15,23,42,0.08)] bg-stone-50 px-5 py-3 text-sm text-stone-700 shadow-sm placeholder:text-stone-400 focus:border-[var(--brand)] focus:bg-white focus:ring-[var(--brand)]"
                                    >
                                </div>
                                <div class="flex items-center gap-3">
                                    @if (request()->filled('search'))
                                        <a href="{{ route('messages.conversation', $user) }}" class="rounded-full border border-[rgba(15,23,42,0.08)] bg-stone-50 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-stone-100">
                                            Reinitialiser
                                        </a>
                                    @endif
                                    <x-primary-button class="justify-center rounded-full px-5 py-3">Rechercher</x-primary-button>
                                </div>
                            </form>
                            <div class="flex flex-wrap items-center justify-between gap-3 text-xs font-medium uppercase tracking-[0.16em] text-stone-400">
                                <span>Discussion avec {{ $user->name }}</span>
                                <a href="{{ route('messages.index') }}" class="text-[var(--brand)] transition hover:text-[var(--brand-deep)]">
                                    Retour a la boite
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="max-h-[68vh] overflow-y-auto bg-[radial-gradient(circle_at_top,rgba(248,250,252,0.95),rgba(255,255,255,1))] px-4 py-5 sm:px-6">
                    <div class="mx-auto max-w-4xl space-y-4">
                        @forelse ($messages as $message)
                            @php
                                $isCurrentUser = $message->sender_id === auth()->id();
                            @endphp
                            <article class="flex {{ $isCurrentUser ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[88%] sm:max-w-[72%]">
                                    @if (! $isCurrentUser)
                                        <div class="mb-2 flex items-center gap-3">
                                            <x-user-avatar :user="$message->sender" class="h-10 w-10 shrink-0 bg-[var(--brand)] text-sm font-semibold uppercase text-white shadow-[0_10px_24px_rgba(79,70,229,0.18)]" />
                                            <div>
                                                <p class="text-sm font-semibold text-stone-900">
                                                    <x-user-link :user="$message->sender">
                                                        {{ $message->sender->name }}
                                                    </x-user-link>
                                                </p>
                                                <p class="text-xs uppercase tracking-[0.16em] text-stone-400">Message recu</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="mb-2 flex items-center justify-end gap-3">
                                            <div class="text-right">
                                                <p class="text-sm font-semibold text-stone-900">Vous</p>
                                                <p class="text-xs uppercase tracking-[0.16em] text-stone-400">Message envoye</p>
                                            </div>
                                            <x-user-avatar :user="$message->sender" class="h-10 w-10 shrink-0 bg-[var(--brand)] text-sm font-semibold uppercase text-white shadow-[0_10px_24px_rgba(79,70,229,0.18)]" />
                                        </div>
                                    @endif

                                    <div class="{{ $isCurrentUser ? 'rounded-[1.8rem_1.8rem_0.55rem_1.8rem] bg-[var(--brand)] text-white' : 'rounded-[1.8rem_1.8rem_1.8rem_0.55rem] bg-stone-100 text-stone-800' }} px-5 py-4 shadow-[0_18px_36px_rgba(15,23,42,0.06)]">
                                        <p class="whitespace-pre-line text-[0.98rem] leading-7">{{ $message->content }}</p>
                                    </div>

                                    <div class="mt-2 flex items-center gap-3 text-xs text-stone-400 {{ $isCurrentUser ? 'justify-end' : 'justify-start pl-1' }}">
                                        <span>{{ $message->created_at->format('d/m/Y H:i') }}</span>
                                        <form method="POST" action="{{ route('messages.destroy', $message) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-semibold uppercase tracking-[0.16em] text-stone-400 transition hover:text-rose-600">
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="mx-auto max-w-xl rounded-[2rem] border border-dashed border-[rgba(15,23,42,0.08)] bg-white px-8 py-14 text-center">
                                <p class="section-kicker">Aucun message</p>
                                <h3 class="mt-3 text-3xl font-semibold text-stone-950">La conversation n'a pas encore commence</h3>
                                <p class="mt-3 text-sm leading-6 text-stone-500">Envoyez le premier message pour lancer l'echange.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="border-t border-[rgba(15,23,42,0.08)] bg-white px-5 py-5 sm:px-6">
                    <div class="mx-auto max-w-4xl">
                        <p class="section-kicker">Repondre</p>
                        <h3 class="mt-3 text-3xl font-semibold text-stone-950">Envoyer un nouveau message</h3>
                        <p class="mt-3 text-sm leading-6 text-stone-500">
                            Ecris un message simple et direct. Il apparaitra tout de suite dans la conversation.
                        </p>
                        <form method="POST" action="{{ route('messages.send') }}" class="mt-6 space-y-4">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                            <div x-data="emojiComposer({ initialValue: @js(old('content')) })" class="rounded-[2rem] border border-[rgba(15,23,42,0.08)] bg-stone-50 p-3">
                                <div class="mb-3">
                                    <x-emoji-toolbar helper="Ajoute une reaction ou une nuance rapide a ton message prive." />
                                </div>
                                <textarea
                                    name="content"
                                    rows="4"
                                    x-ref="input"
                                    x-model="value"
                                    placeholder="Ecris ton message..."
                                    class="block w-full rounded-[1.4rem] border border-[rgba(15,23,42,0.08)] bg-white px-4 py-4 text-sm shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                                    required
                                ></textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('content')" />
                            </div>
                            <div class="flex justify-end">
                                <x-primary-button class="rounded-full px-6 py-3">Envoyer</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
