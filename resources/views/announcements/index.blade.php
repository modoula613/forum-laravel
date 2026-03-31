<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Annonces</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Informations globales</h2>
            <p class="muted-copy mt-3 text-base leading-7">
                Retrouve les annonces actives et les informations importantes partagees sur Sphere.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="space-y-4">
                @forelse ($announcements as $announcement)
                    <article class="glass-panel rounded-[2rem] p-6">
                        <div class="space-y-3">
                            <h3 class="text-2xl font-semibold text-stone-950">{{ $announcement->title }}</h3>
                            <p class="text-sm leading-7 text-stone-600">{{ $announcement->content }}</p>
                            <p class="text-xs uppercase tracking-[0.16em] text-stone-500">
                                Publiee le {{ $announcement->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </article>
                @empty
                    <div class="glass-panel rounded-[2rem] border-dashed p-8 text-center text-sm text-stone-500">
                        Aucune annonce active pour le moment.
                    </div>
                @endforelse
            </div>

            <div class="glass-panel rounded-[2rem] px-4 py-4 sm:px-6">
                {{ $announcements->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
