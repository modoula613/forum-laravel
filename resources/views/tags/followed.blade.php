<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Tags suivis</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Mes tags suivis</h2>
            <p class="muted-copy mt-3 text-base leading-7">
                Retrouve ici les tags que tu suis pour revenir vite sur les discussions qui t’interessent.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            @forelse ($tags as $tag)
                <article class="glass-panel-strong rounded-[2rem] p-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="section-kicker">Tag</p>
                            <h3 class="mt-3 text-2xl font-semibold text-stone-950">{{ $tag->name }}</h3>
                            <p class="mt-3 text-sm text-stone-500">{{ $tag->topics_count }} sujet(s)</p>
                        </div>
                        <a href="{{ route('tags.show', $tag) }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-white">
                            Ouvrir
                        </a>
                    </div>
                </article>
            @empty
                <div class="glass-panel rounded-[2rem] border-dashed p-12 text-center">
                    <p class="section-kicker">Aucun suivi</p>
                    <h3 class="mt-3 text-3xl font-semibold text-stone-950">Tu ne suis encore aucun tag</h3>
                    <p class="muted-copy mt-3 text-base">Suis des tags pour personnaliser ton flux.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
