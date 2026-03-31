<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <p class="section-kicker">Brouillons</p>
                <h2 class="mt-3 text-4xl font-semibold text-stone-950">Mes sujets en attente</h2>
                <p class="muted-copy mt-3 text-base leading-7">
                    Retrouve tes sujets non publies, modifie-les ou mets-les en ligne quand ils sont prets.
                </p>
            </div>
            <a
                href="{{ route('topics.create') }}"
                class="inline-flex items-center rounded-full bg-[var(--brand)] px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-white shadow-[0_18px_35px_rgba(79,70,229,0.28)] transition hover:-translate-y-0.5 hover:bg-[var(--brand-deep)]"
            >
                Nouveau sujet
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

            <div class="space-y-4">
                @forelse ($topics as $topic)
                    <article class="glass-panel rounded-[2rem] p-6">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-700">Brouillon</span>
                                    @if ($topic->category)
                                        <span class="rounded-full bg-[rgba(20,184,166,0.12)] px-3 py-1 text-[var(--accent)]">
                                            {{ $topic->category->name }}
                                        </span>
                                    @endif
                                    <span>{{ $topic->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <h3 class="text-2xl font-semibold text-stone-950">{{ $topic->title }}</h3>
                                <p class="text-sm leading-7 text-stone-600">
                                    {{ \Illuminate\Support\Str::limit($topic->content, 180) }}
                                </p>
                            </div>
                            <div class="flex flex-wrap items-center gap-3">
                                <a href="{{ route('topics.edit', $topic) }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                                    Modifier
                                </a>
                                <form method="POST" action="{{ route('topics.publish', $topic) }}">
                                    @csrf
                                    @method('PATCH')
                                    <x-primary-button type="submit">Publier</x-primary-button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="glass-panel rounded-[2rem] border-dashed p-8 text-center text-sm text-stone-500">
                        Aucun brouillon pour le moment.
                    </div>
                @endforelse
            </div>

            <div class="glass-panel rounded-[2rem] px-4 py-4 sm:px-6">
                {{ $topics->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
