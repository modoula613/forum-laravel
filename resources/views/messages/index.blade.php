<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Messages</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Boite de reception</h2>
            <p class="muted-copy mt-3 text-base leading-7">
                Retrouve ici les messages prives recus des autres membres.
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

            @forelse ($conversations as $conversation)
                <article class="glass-panel rounded-[2rem] p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-stone-500">Utilisateur concerne</p>
                            <h3 class="mt-2 text-2xl font-semibold text-stone-950">{{ $conversation->user->name }}</h3>
                            <p class="mt-4 whitespace-pre-line text-base leading-8 text-stone-700">{{ $conversation->last_message->content }}</p>
                        </div>
                        <div class="text-right text-sm text-stone-500">
                            <p>{{ $conversation->last_message->created_at->format('d/m/Y H:i') }}</p>
                            @if ($conversation->unread_count > 0)
                                <div class="mt-3">
                                    <span class="inline-flex rounded-full bg-sky-100 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-sky-700">
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
                    <p class="section-kicker">Aucun message</p>
                    <h3 class="mt-3 text-3xl font-semibold text-stone-950">Ta boite est vide</h3>
                </div>
            @endforelse

            <div class="glass-panel rounded-[2rem] px-4 py-4 sm:px-6">
                {{ $conversations->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
