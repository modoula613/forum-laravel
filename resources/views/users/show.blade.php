<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="section-kicker">Profil public</p>
                <h2 class="mt-3 text-4xl font-semibold text-stone-950">{{ $user->name }}</h2>
                <p class="mt-3 text-sm text-stone-500">
                    Inscrit le {{ $user->created_at->format('d/m/Y') }} · {{ $user->topics_count }} sujets · {{ $user->replies_count }} reponses
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
            <section class="glass-panel rounded-[2rem] p-6">
                <div class="flex items-center gap-4">
                    <span class="flex h-16 w-16 items-center justify-center rounded-full bg-[var(--brand)] text-xl font-semibold uppercase text-white shadow-[0_10px_24px_rgba(79,70,229,0.22)]">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </span>
                    <div>
                        <p class="text-2xl font-semibold text-stone-950">{{ $user->name }}</p>
                        @if ($user->replies_count > 10)
                            <span class="mt-2 inline-flex rounded-full bg-[rgba(79,70,229,0.12)] px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[var(--brand)]">
                                Utilisateur actif
                            </span>
                        @endif
                    </div>
                </div>

                <div class="mt-6 grid gap-4">
                    <div class="rounded-[1.5rem] bg-white/70 p-4">
                        <p class="text-sm text-stone-500">Niveau</p>
                        <p class="mt-2 text-2xl font-semibold text-stone-950">Niveau {{ $user->level }}</p>
                        <p class="mt-2 text-sm text-stone-600">{{ $user->experience }} / {{ $user->level * 100 }} XP</p>
                        <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-stone-200">
                            <div
                                class="h-full rounded-full bg-[linear-gradient(135deg,var(--brand),var(--accent-soft))]"
                                style="width: {{ min(100, ($user->experience / max(1, $user->level * 100)) * 100) }}%;"
                            ></div>
                        </div>
                    </div>
                    <div class="rounded-[1.5rem] bg-white/70 p-4">
                        <p class="text-sm text-stone-500">Reputation</p>
                        <p class="mt-2 text-2xl font-semibold text-stone-950">{{ $user->reputation }}</p>
                    </div>
                    <div class="rounded-[1.5rem] bg-white/70 p-4">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm text-stone-500">Activite</p>
                                <p class="mt-2 text-base text-stone-700">Voir les dernieres actions du membre.</p>
                            </div>
                            <a href="{{ route('users.activity', $user) }}" class="text-xs font-semibold uppercase tracking-[0.16em] text-[var(--brand)] transition hover:text-[var(--brand-deep)]">
                                Ouvrir
                            </a>
                        </div>
                    </div>
                    <div class="rounded-[1.5rem] bg-white/70 p-4">
                        <p class="text-sm text-stone-500">Sujets</p>
                        <p class="mt-2 text-2xl font-semibold text-stone-950">{{ $user->topics_count }}</p>
                    </div>
                    <div class="rounded-[1.5rem] bg-white/70 p-4">
                        <p class="text-sm text-stone-500">Reponses</p>
                        <p class="mt-2 text-2xl font-semibold text-stone-950">{{ $user->replies_count }}</p>
                    </div>
                    <div class="rounded-[1.5rem] bg-white/70 p-4">
                        <p class="text-sm text-stone-500">Reponses sauvegardees</p>
                        <p class="mt-2 text-2xl font-semibold text-stone-950">{{ $user->bookmarkedReplies()->count() }}</p>
                    </div>
                </div>

                <div class="mt-6 rounded-[1.5rem] bg-white/70 p-4">
                    <div class="flex items-center justify-between gap-4">
                        <p class="text-sm text-stone-500">Badges</p>
                        <a href="{{ route('users.badges', $user) }}" class="text-xs font-semibold uppercase tracking-[0.16em] text-[var(--brand)] transition hover:text-[var(--brand-deep)]">
                            Voir tout
                        </a>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @forelse ($user->badges as $badge)
                            <a
                                href="{{ route('users.badges', $user) }}"
                                title="{{ $badge->description }}"
                                class="rounded-full bg-[rgba(79,70,229,0.12)] px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[var(--brand)] transition hover:bg-[rgba(79,70,229,0.18)]"
                            >
                                {{ $badge->name }}
                            </a>
                        @empty
                            <span class="text-sm text-stone-500">Aucun badge pour le moment.</span>
                        @endforelse
                    </div>
                </div>

                @auth
                    @if (auth()->user()->role === 'admin')
                        <div class="mt-6 rounded-[1.5rem] bg-white/70 p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm text-stone-500">Moderation admin</p>
                                    <p class="mt-2 text-base text-stone-700">Gere rapidement le statut de ce compte.</p>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <form method="POST" action="{{ route('admin.users.toggleBlock', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-full bg-rose-600 px-4 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-white transition hover:bg-rose-500">
                                            {{ $user->is_blocked ? 'Debloquer' : 'Bloquer' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ $user->is_banned ? route('admin.users.unban', $user) : route('admin.users.ban', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-full bg-stone-900 px-4 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-white transition hover:bg-stone-800">
                                            {{ $user->is_banned ? 'Debannir' : 'Bannir' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @endauth
            </section>

            <section class="glass-panel-strong rounded-[2rem] p-6 sm:p-8">
                <p class="section-kicker">Contacter</p>
                <h3 class="mt-3 text-3xl font-semibold text-stone-950">Envoyer un message prive</h3>

                @if (session('success'))
                    <div class="mt-4 rounded-[1.5rem] border border-emerald-200 bg-emerald-50/90 px-5 py-4 text-sm text-emerald-900">
                        {{ session('success') }}
                    </div>
                @endif

                @auth
                    @if (auth()->id() !== $user->id)
                        <form method="POST" action="{{ route('messages.send') }}" class="mt-6 space-y-4">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                            <div>
                                <x-input-label for="content" :value="__('Message')" />
                                <textarea
                                    id="content"
                                    name="content"
                                    rows="6"
                                    class="mt-1 block w-full rounded-[1.5rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-4 shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                                    required
                                >{{ old('content') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('content')" />
                            </div>
                            <div class="flex justify-end">
                                <x-primary-button>Envoyer un message</x-primary-button>
                            </div>
                        </form>
                    @else
                        <div class="mt-6 rounded-[1.5rem] bg-white/70 p-5 text-sm text-stone-600">
                            Tu ne peux pas t'envoyer un message a toi-meme.
                        </div>
                    @endif
                @else
                    <div class="mt-6 rounded-[1.5rem] bg-white/70 p-5 text-sm text-stone-600">
                        <a href="{{ route('login') }}" class="font-semibold text-stone-900 underline underline-offset-4">Connecte-toi</a>
                        pour envoyer un message prive.
                    </div>
                @endauth
            </section>
        </div>
    </div>
</x-app-layout>
