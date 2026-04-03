<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="section-kicker">Conversation</p>
                <h2 class="mt-3 text-4xl font-semibold text-stone-950">Discussion avec {{ $user->name }}</h2>
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
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="glass-panel rounded-[1.5rem] border-emerald-200 bg-emerald-50/90 px-5 py-4 text-sm text-emerald-900">
                    {{ session('success') }}
                </div>
            @endif

            <section class="glass-panel rounded-[2rem] p-5 sm:p-6">
                <form method="GET" action="{{ route('messages.conversation', $user) }}" class="flex flex-col gap-4 sm:flex-row sm:items-center">
                    <div class="flex-1">
                        <label for="search" class="sr-only">Rechercher dans la conversation</label>
                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Rechercher dans la conversation..."
                            class="block w-full rounded-[1.5rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                        >
                    </div>
                    <div class="flex items-center gap-3">
                        @if (request()->filled('search'))
                            <a href="{{ route('messages.conversation', $user) }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                                Reinitialiser
                            </a>
                        @endif
                        <x-primary-button>Rechercher</x-primary-button>
                    </div>
                </form>
            </section>

            <section class="space-y-4">
                @forelse ($messages as $message)
                    <article class="glass-panel rounded-[2rem] p-5 {{ $message->sender_id === auth()->id() ? 'border-[rgba(79,70,229,0.18)]' : '' }}">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-stone-600">
                                    {{ $message->sender_id === auth()->id() ? 'Vous' : $message->sender->name }}
                                </p>
                                <p class="mt-1 text-xs font-medium uppercase tracking-[0.16em] text-stone-500">
                                    {{ $message->sender_id === auth()->id() ? 'Message envoye' : 'Message recu' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-stone-500">{{ $message->created_at->format('d/m/Y H:i') }}</p>
                                <form method="POST" action="{{ route('messages.destroy', $message) }}" class="mt-3">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-full border border-rose-200 bg-rose-50/90 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-rose-700 transition hover:bg-rose-100">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                        <p class="mt-4 whitespace-pre-line text-base leading-8 text-stone-700">{{ $message->content }}</p>
                    </article>
                @empty
                    <div class="glass-panel rounded-[2rem] border-dashed p-12 text-center">
                        <p class="section-kicker">Aucun message</p>
                        <h3 class="mt-3 text-3xl font-semibold text-stone-950">La conversation n'a pas encore commence</h3>
                    </div>
                @endforelse
            </section>

            <section class="glass-panel-strong rounded-[2rem] p-6 sm:p-8">
                <p class="section-kicker">Repondre</p>
                <h3 class="mt-3 text-3xl font-semibold text-stone-950">Envoyer un nouveau message</h3>
                <form method="POST" action="{{ route('messages.send') }}" class="mt-6 space-y-4">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                    <div x-data="emojiComposer({ initialValue: @js(old('content')) })">
                        <div class="mb-3">
                            <x-emoji-toolbar helper="Ajoute une reaction ou une nuance rapide a ton message prive." />
                        </div>
                        <textarea
                            name="content"
                            rows="5"
                            x-ref="input"
                            x-model="value"
                            class="block w-full rounded-[1.5rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-4 shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                            required
                        ></textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('content')" />
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>Envoyer</x-primary-button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
