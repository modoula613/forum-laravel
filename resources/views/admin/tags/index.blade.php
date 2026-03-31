<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Administration</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Gestion des tags</h2>
            <p class="mt-3 text-sm text-stone-500">
                Surveille les tags les plus utilises et supprime ceux qui ne sont plus souhaites.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-8 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="glass-panel rounded-[1.5rem] border-emerald-200 bg-emerald-50/90 px-5 py-4 text-sm text-emerald-900">
                    {{ session('success') }}
                </div>
            @endif

            <section class="space-y-4">
                @forelse ($tags as $tag)
                    <article class="glass-panel rounded-[2rem] p-5 sm:p-6">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-semibold text-stone-950">{{ $tag->name }}</h3>
                                <p class="mt-2 text-sm text-stone-500">{{ $tag->topics_count }} sujets</p>
                            </div>
                            <form method="POST" action="{{ route('admin.tags.delete', $tag) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-full bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500">
                                    Supprimer tag
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="glass-panel rounded-[2rem] border-dashed p-8 text-center text-sm text-stone-500">
                        Aucun tag disponible.
                    </div>
                @endforelse
            </section>

            <div>
                {{ $tags->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
