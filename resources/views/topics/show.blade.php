<x-app-layout :meta-description="\Illuminate\Support\Str::limit($topic->content, 150)">
    <x-slot name="header">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-3xl">
                <p class="section-kicker">Sujet en cours</p>
                <h2 class="mt-3 text-4xl font-semibold text-stone-950">{{ $topic->title }}</h2>
                <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-stone-500">
                    <span>Par <span class="font-semibold text-stone-700">{{ $topic->user->name }}</span> le {{ $topic->created_at->format('d/m/Y H:i') }}</span>
                    @if ($topic->edits_count > 0)
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">
                            Modifie
                        </span>
                    @endif
                    @if ($topic->is_locked)
                        <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-rose-700">
                            Sujet verrouille
                        </span>
                    @endif
                    @if ($topic->is_pinned)
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">
                            Epingle
                        </span>
                    @endif
                    @if ($topic->category)
                        <a href="{{ route('categories.show', $topic->category) }}" class="rounded-full bg-[rgba(20,184,166,0.12)] px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-[var(--accent)] transition hover:bg-[rgba(20,184,166,0.2)]">
                            {{ $topic->category->name }}
                        </a>
                    @endif
                </div>
            </div>
            @auth
                <div class="flex flex-wrap items-center gap-3 lg:justify-end">
                    <form method="POST" action="{{ route('topics.favorite', $topic) }}">
                        @csrf
                        <button type="submit" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                            {{ $topic->favorites->contains('user_id', auth()->id()) ? 'Ne plus suivre' : 'Suivre' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('topics.report', $topic) }}">
                        @csrf
                        <input type="hidden" name="reason" value="Signalement du sujet">
                        <button type="submit" class="rounded-full border border-rose-200 bg-rose-50/90 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100">
                            Signaler
                        </button>
                    </form>
                    @if ($topic->user_id === auth()->id())
                        <a href="{{ route('topics.history', $topic) }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                            Historique
                        </a>
                        <a href="{{ route('topics.edit', $topic) }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                            Modifier
                        </a>
                        <form method="POST" action="{{ route('topics.destroy', $topic) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-full bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500">
                                Supprimer
                            </button>
                        </form>
                    @endif
                    @if (auth()->user()->role === 'admin')
                        <form method="POST" action="{{ route('admin.topics.pin', $topic) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                                {{ $topic->is_pinned ? 'Desepingler' : 'Epingler' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.topics.lock', $topic) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                                {{ $topic->is_locked ? 'Deverrouiller' : 'Verrouiller' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.topics.destroy', $topic) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-full bg-amber-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-amber-400">
                                Supprimer (admin)
                            </button>
                        </form>
                    @endif
                </div>
            @endauth
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-8 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="glass-panel rounded-[1.5rem] border-emerald-200 bg-emerald-50/90 px-5 py-4 text-sm text-emerald-900">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="glass-panel rounded-[1.5rem] border-rose-200 bg-rose-50/90 px-5 py-4 text-sm text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            @auth
                @if (auth()->user()->is_blocked)
                    <div class="glass-panel rounded-[2rem] border-rose-200 bg-rose-50/90 p-6 text-sm font-medium text-rose-800">
                        Votre compte est bloque suite a plusieurs infractions.
                    </div>
                @endif
            @endauth

            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_20rem]">
                <article class="glass-panel-strong rounded-[2rem] p-7 sm:p-8">
                    <div class="mb-6 flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.2em] text-stone-500">
                        <span class="rounded-full bg-[rgba(79,70,229,0.12)] px-3 py-1 text-[var(--brand)]">Discussion ouverte</span>
                        <span>{{ $topic->replies->count() }} message(s)</span>
                        <span>{{ $topic->favorites_count }} abonnes</span>
                        @foreach ($topic->tags as $tag)
                            <a href="{{ route('tags.show', $tag) }}" class="rounded-full bg-[rgba(79,70,229,0.1)] px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-[var(--brand)] transition hover:bg-[rgba(79,70,229,0.16)]">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                    <div class="prose max-w-none text-stone-700">
                        <p>{!! nl2br(e($topic->content)) !!}</p>
                    </div>
                </article>

                <aside class="glass-panel rounded-[2rem] p-6">
                    <p class="section-kicker">Vue rapide</p>
                    <div class="mt-4 grid gap-4">
                        <div class="rounded-[1.5rem] bg-white/70 p-5">
                            <p class="text-sm text-stone-500">Auteur</p>
                            <div class="mt-3 flex items-center gap-3">
                                <span class="flex h-11 w-11 items-center justify-center rounded-full bg-[var(--brand)] text-sm font-semibold uppercase text-white shadow-[0_10px_24px_rgba(79,70,229,0.22)]">
                                    {{ strtoupper(substr($topic->user->name, 0, 1)) }}
                                </span>
                                <div>
                                    <p class="text-2xl font-semibold text-stone-950">{{ $topic->user->name }}</p>
                                    <p class="mt-1 text-xs font-semibold uppercase tracking-[0.16em] text-[var(--brand)]">
                                        Lvl {{ $topic->user->level }} · {{ $topic->user->experience }} / {{ $topic->user->level * 100 }} XP
                                    </p>
                                    <p class="mt-1 text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">
                                        Rep {{ $topic->user->reputation }}
                                    </p>
                                    @if ($topic->user->repliesCount() > 10)
                                        <span class="mt-1 inline-flex rounded-full bg-[rgba(79,70,229,0.12)] px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[var(--brand)]">
                                            Utilisateur actif
                                        </span>
                                    @endif
                                    @if ($topic->user->badges->isNotEmpty())
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            @foreach ($topic->user->badges as $badge)
                                                <span
                                                    title="{{ $badge->description }}"
                                                    class="rounded-full bg-[rgba(79,70,229,0.12)] px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-[var(--brand)]"
                                                >
                                                    {{ $badge->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="rounded-[1.5rem] bg-white/70 p-5">
                            <p class="text-sm text-stone-500">Participation auteur</p>
                            <p class="mt-2 text-lg font-semibold text-stone-950">{{ $topic->user->topicsCount() }} sujets</p>
                            <p class="mt-1 text-sm text-stone-600">{{ $topic->user->repliesCount() }} reponses</p>
                        </div>
                        <div class="rounded-[1.5rem] bg-white/70 p-5">
                            <p class="text-sm text-stone-500">Discussion</p>
                            <p class="mt-2 text-lg font-semibold text-stone-950">{{ $topic->replies->count() }} messages</p>
                            <p class="mt-1 text-sm text-stone-600">{{ $topic->favorites_count }} abonnes</p>
                        </div>
                    </div>
                </aside>
            </div>

            <section class="space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-3xl font-semibold text-stone-950">Reponses</h3>
                    <span class="rounded-full bg-white/70 px-4 py-2 text-sm font-medium text-stone-600">{{ $topic->replies->count() }} message(s)</span>
                </div>

                @forelse ($topic->replies as $reply)
                    <article class="glass-panel rounded-[1.85rem] p-5 sm:p-6">
                        <div class="flex gap-4">
                            <span class="mt-1 flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[rgba(139,92,246,0.12)] text-sm font-semibold uppercase text-[var(--brand)]">
                                {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-stone-900">{{ $reply->user->name }}</p>
                                        <p class="mt-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-[var(--brand)]">
                                            Lvl {{ $reply->user->level }} · Rep {{ $reply->user->reputation }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-stone-500">{{ $reply->created_at->format('d/m/Y H:i') }}</p>
                                        @if ($reply->edits_count > 0)
                                            <span class="mt-2 inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-amber-700">
                                                Modifiee
                                            </span>
                                        @endif
                                        @if ($reply->reports_count > 0)
                                            <span class="mt-2 inline-flex rounded-full bg-rose-100 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-rose-700">
                                                Signalee
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <p class="mt-4 whitespace-pre-line text-base leading-8 text-stone-700">{{ $reply->content }}</p>
                                <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
                                    @auth
                                        <div class="flex flex-wrap items-center gap-3">
                                            <form method="POST" action="{{ route('replies.like', $reply) }}">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center gap-2 rounded-full border border-[rgba(139,92,246,0.18)] bg-white/80 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:border-[var(--brand)] hover:text-[var(--brand)]"
                                                >
                                                    <span>{{ $reply->likes->contains('user_id', auth()->id()) ? 'Aime' : 'Like' }}</span>
                                                    <span>{{ $reply->likes_count }}</span>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('replies.report', $reply) }}">
                                                @csrf
                                                <input type="hidden" name="reason" value="Signalement de la reponse">
                                                <button type="submit" class="rounded-full border border-rose-200 bg-rose-50/90 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100">
                                                    Signaler
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('replies.bookmark', $reply) }}">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white"
                                                >
                                                    {{ $reply->bookmarkedBy->contains('id', auth()->id()) ? 'Retirer le signet' : 'Sauvegarder' }}
                                                </button>
                                            </form>
                                            @if (auth()->id() === $reply->user_id)
                                                <a href="{{ route('replies.history', $reply) }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                                                    Historique
                                                </a>
                                                <details class="group">
                                                    <summary class="cursor-pointer list-none rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                                                        Modifier
                                                    </summary>
                                                    <form method="POST" action="{{ route('replies.update', $reply) }}" class="mt-4 space-y-3" x-data="emojiComposer({ initialValue: @js($reply->content) })">
                                                        @csrf
                                                        @method('PUT')
                                                        <x-emoji-toolbar helper="Ajoute un emoji si tu veux nuancer rapidement la reponse." />
                                                        <textarea
                                                            name="content"
                                                            rows="4"
                                                            x-ref="input"
                                                            x-model="value"
                                                            class="block w-full rounded-[1.25rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-4 text-sm shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                                                            required
                                                        ></textarea>
                                                        <div class="flex justify-end">
                                                            <x-primary-button>Enregistrer</x-primary-button>
                                                        </div>
                                                    </form>
                                                </details>
                                            @endif
                                            @if (auth()->id() === $reply->user_id || auth()->user()->role === 'admin')
                                                <form method="POST" action="{{ route('replies.destroy', $reply) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="rounded-full bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500"
                                                    >
                                                        Supprimer
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-2 rounded-full bg-white/70 px-4 py-2 text-sm font-medium text-stone-500">
                                            Likes {{ $reply->likes_count }}
                                        </span>
                                    @endauth
                                </div>
                            </div>
                        </div>
                        @if ($reply->reports_count > 0)
                            <p class="mt-2 text-xs font-semibold uppercase tracking-[0.16em] text-rose-700">
                                {{ $reply->reports_count }} signalement(s)
                            </p>
                        @endif
                    </article>
                @empty
                    <div class="glass-panel rounded-[2rem] border-dashed p-8 text-center text-sm text-stone-500">
                        Pas encore de reponse.
                    </div>
                @endforelse
            </section>

            @auth
                @if ($topic->is_locked)
                    <div class="glass-panel rounded-[2rem] border-rose-200 bg-rose-50/90 p-6 text-sm font-medium text-rose-800">
                        Ce sujet est verrouille et ne peut plus recevoir de reponses.
                    </div>
                @elseif (! auth()->user()->is_blocked)
                    <section class="glass-panel-strong rounded-[2.25rem] p-6 sm:p-8">
                        <p class="section-kicker">Participer</p>
                        <h3 class="mt-3 text-3xl font-semibold text-stone-950">Ajouter une reponse</h3>
                        <form method="POST" action="{{ route('replies.store', $topic) }}" class="mt-4 space-y-4" x-data="emojiComposer({ initialValue: @js(old('content')) })">
                            @csrf
                            <div>
                                <div class="mb-3">
                                    <x-emoji-toolbar helper="Un emoji peut rendre la reponse plus naturelle sans la surcharger." />
                                </div>
                                <textarea
                                    id="content"
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
                                <x-primary-button>Publier la reponse</x-primary-button>
                            </div>
                        </form>
                    </section>
                @endif
            @else
                <div class="glass-panel rounded-[2rem] p-6 text-sm text-stone-600">
                    <a href="{{ route('login') }}" class="font-semibold text-stone-900 underline underline-offset-4">Connecte-toi</a>
                    pour participer a la discussion.
                </div>
            @endauth
        </div>
    </div>
</x-app-layout>
