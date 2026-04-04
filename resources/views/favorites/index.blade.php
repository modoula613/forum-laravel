<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Favoris</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Mes sujets suivis</h2>
            <p class="muted-copy mt-3 text-base leading-7">
                Retrouve ici les discussions que tu suis pour y revenir rapidement.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            @forelse ($topics as $topic)
                <article class="glass-panel-strong rounded-[2rem] p-6">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        <div class="space-y-3">
                            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">
                                @if ($topic->category)
                                    <span class="rounded-full bg-[rgba(20,184,166,0.12)] px-3 py-1 text-[var(--accent)]">{{ $topic->category->name }}</span>
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
                                        <span class="rounded-full bg-[rgba(79,70,229,0.1)] px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[var(--brand)]">
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <a href="{{ route('topics.show', $topic) }}" class="inline-flex items-center rounded-full bg-[var(--brand)] px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-white shadow-[0_18px_35px_rgba(79,70,229,0.28)] transition hover:-translate-y-0.5 hover:bg-[var(--brand-deep)]">
                            Ouvrir
                        </a>
                    </div>
                </article>
            @empty
                <div class="glass-panel rounded-[2rem] border-dashed p-12 text-center">
                    <p class="section-kicker">Aucun favori</p>
                    <h3 class="mt-3 text-3xl font-semibold text-stone-950">Tu ne suis encore aucun sujet</h3>
                    <p class="muted-copy mt-3 text-base">Ajoute des sujets a tes favoris depuis leur page pour les retrouver ici.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
