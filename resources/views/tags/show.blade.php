<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="section-kicker">Tag</p>
                <h2 class="mt-3 text-4xl font-semibold text-stone-950">{{ $tag->name }}</h2>
                <p class="muted-copy mt-3 text-base leading-7">
                    Les discussions liees a ce tag, avec leur auteur et leur niveau d'activite.
                </p>
                <p class="mt-3 text-sm text-stone-500">{{ $tag->followers_count }} abonne(s)</p>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    <form method="POST" action="{{ route('tags.follow', $tag) }}">
                        @csrf
                        <button type="submit" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-white">
                            {{ auth()->user()->followedTags->contains($tag->id) ? 'Ne plus suivre' : 'Suivre' }}
                        </button>
                    </form>
                @endauth
                <a href="{{ route('tags.index') }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-white">
                    Tous les tags
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            @forelse ($topics as $topic)
                <article class="glass-panel-strong rounded-[2rem] p-6 sm:p-7">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        <div class="space-y-3">
                            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">
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
                                Par <span class="font-semibold text-stone-700">{{ $topic->user->name }}</span>
                            </p>
                            @if ($topic->tags->isNotEmpty())
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($topic->tags as $topicTag)
                                        <a href="{{ route('tags.show', $topicTag) }}" class="rounded-full bg-[rgba(79,70,229,0.1)] px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[var(--brand)] transition hover:bg-[rgba(79,70,229,0.16)]">
                                            {{ $topicTag->name }}
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
                            <div class="rounded-full bg-white/80 px-4 py-2 text-center text-xs font-semibold uppercase tracking-[0.18em] text-stone-700">
                                {{ $topic->favorites_count }} abonnes
                            </div>
                            <a href="{{ route('topics.show', $topic) }}" class="block rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-2 text-center text-sm font-semibold text-stone-800 transition hover:bg-white">
                                Ouvrir
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="glass-panel rounded-[2rem] border-dashed p-12 text-center">
                    <p class="section-kicker">Aucun sujet</p>
                    <h3 class="mt-3 text-3xl font-semibold text-stone-950">Aucune discussion pour ce tag</h3>
                </div>
            @endforelse

            <div class="glass-panel rounded-[2rem] px-4 py-4 sm:px-6">
                {{ $topics->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
