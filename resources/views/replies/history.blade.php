<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="section-kicker">Historique reponse</p>
                <h2 class="mt-3 text-4xl font-semibold text-stone-950">Modifications de la reponse</h2>
                <p class="muted-copy mt-3 text-base leading-7">
                    Les anciennes versions de cette reponse sont conservees ici.
                </p>
            </div>
            <a href="{{ route('topics.show', $reply->topic) }}" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/70 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                Retour a la discussion
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="space-y-4">
                @forelse ($edits as $edit)
                    <article class="glass-panel rounded-[2rem] p-6">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-[var(--brand)]">Version precedente</p>
                            <p class="text-sm text-stone-500">{{ $edit->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="mt-4 rounded-[1.5rem] bg-white/70 p-5">
                            <p class="whitespace-pre-line text-sm leading-7 text-stone-700">{{ $edit->old_content }}</p>
                        </div>
                    </article>
                @empty
                    <div class="glass-panel rounded-[2rem] border-dashed p-8 text-center text-sm text-stone-500">
                        Aucune modification enregistree pour cette reponse.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
