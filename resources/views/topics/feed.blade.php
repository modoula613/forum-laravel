<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="section-kicker">Flux</p>
                <h2 class="mt-3 text-4xl font-semibold text-stone-950">Mon flux</h2>
                <p class="muted-copy mt-3 text-base leading-7">
                    Les derniers sujets publies par les membres que tu suis.
                </p>
            </div>
            <a href="{{ route('users.index') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-white">
                Trouver des membres
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            @forelse ($topics as $topic)
                <article class="glass-panel-strong rounded-[2rem] p-6 sm:p-7">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        <div class="space-y-3">
                            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-700">Suivi</span>
                                @if ($topic->category)
                                    <a href="{{ route('categories.show', $topic->category) }}" class="rounded-full bg-[rgba(20,184,166,0.12)] px-3 py-1 text-[var(--accent)] transition hover:bg-[rgba(20,184,166,0.2)]">
                                        {{ $topic->category->name }}
                                    </a>
                                @endif
                                <span>{{ $topic->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <h3 class="text-3xl font-semibold text-stone-950">
                                <a href="{{ route('topics.show', $topic) }}" class="transition hover:text-[var(--brand-deep)]">
                                    {{ $topic->title }}
                                </a>
                            </h3>
                            <p class="text-sm text-stone-500">
                                Par
                                <x-user-link :user="$topic->user" class="font-semibold text-stone-700">
                                    {{ $topic->user->name }}
                                </x-user-link>
                            </p>
                            @if ($topic->tags->isNotEmpty())
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($topic->tags as $tag)
                                        <a href="{{ route('tags.show', $tag) }}" class="rounded-full bg-[rgba(79,70,229,0.1)] px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[var(--brand)] transition hover:bg-[rgba(79,70,229,0.16)]">
                                            {{ $tag->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="shrink-0 space-y-3 text-right">
                            <div class="rounded-[1.5rem] bg-[linear-gradient(135deg,var(--brand),var(--accent-soft))] px-5 py-4 text-center text-white shadow-[0_18px_35px_rgba(79,70,229,0.24)]">
                                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.24em] text-white/75">Reponses</p>
                                <p class="mt-2 text-3xl font-semibold">{{ $topic->replies_count }}</p>
                            </div>
                            <a href="{{ route('topics.show', $topic) }}" class="block rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-2 text-center text-sm font-semibold text-stone-800 transition hover:bg-white">
                                Ouvrir
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <section class="forum-empty-state p-8 sm:p-10">
                    <div class="mx-auto max-w-3xl text-center">
                        <span class="forum-empty-state__icon mx-auto">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill="currentColor" d="M17 7a3 3 0 1 1-2.999 3A3 3 0 0 1 17 7Zm-10 1a3 3 0 1 1-3 3 3 3 0 0 1 3-3Zm10 7c2.761 0 5 1.567 5 3.5V20h-8v-1.5c0-.941.43-1.8 1.136-2.474A7.57 7.57 0 0 1 17 15Zm-10 1c3.314 0 6 1.79 6 4v1H1v-1c0-2.21 2.686-4 6-4Z"/>
                            </svg>
                        </span>
                        <p class="section-kicker mt-5">Flux vide</p>
                        <h3 class="forum-empty-state__title mt-3 text-3xl font-semibold">
                            {{ $followedUserIds->isEmpty() ? 'Tu ne suis encore personne' : 'Aucun sujet recent dans tes suivis' }}
                        </h3>
                        <p class="forum-empty-state__copy mt-3 text-base leading-7">
                            {{ $followedUserIds->isEmpty() ? 'Suis quelques membres pour voir apparaitre ici uniquement leurs sujets.' : 'Les membres que tu suis n’ont pas encore publie de nouveau sujet.' }}
                        </p>
                        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                            <a href="{{ route('users.index') }}" class="inline-flex items-center rounded-full bg-[var(--brand)] px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-white transition hover:bg-[var(--brand-deep)]">
                                Decouvrir des membres
                            </a>
                            <a href="{{ route('topics.index') }}" class="inline-flex items-center rounded-full border border-[var(--line)] px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-[var(--content-strong)] transition hover:bg-[var(--surface-soft-hover)]">
                                Retour au forum
                            </a>
                        </div>
                    </div>
                </section>
            @endforelse

            <div class="glass-panel rounded-[2rem] px-4 py-4 sm:px-6">
                {{ $topics->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
