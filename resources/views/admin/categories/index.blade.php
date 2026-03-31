<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Administration</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Gestion des categories</h2>
            <p class="mt-3 text-sm text-stone-500">
                Cree, modifie ou supprime les categories qui structurent le forum.
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

            <section class="glass-panel rounded-[2rem] p-6">
                <p class="section-kicker">Nouvelle categorie</p>
                <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-5 grid gap-4 lg:grid-cols-[1fr_1.4fr_auto] lg:items-end">
                    @csrf
                    <div>
                        <x-input-label for="name" :value="__('Nom')" />
                        <input id="name" type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-[1.25rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]" required>
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>
                    <div>
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-[1.25rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]">{{ old('description') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>
                    <x-primary-button class="justify-center">Creer</x-primary-button>
                </form>
            </section>

            <section class="space-y-4">
                @forelse ($categories as $category)
                    <article class="glass-panel rounded-[2rem] p-5 sm:p-6">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                            <div class="max-w-3xl">
                                <h3 class="text-xl font-semibold text-stone-950">{{ $category->name }}</h3>
                                <p class="mt-2 text-sm text-stone-500">{{ $category->topics_count }} sujets</p>
                                @if ($category->description)
                                    <p class="mt-3 text-sm leading-7 text-stone-700">{{ $category->description }}</p>
                                @endif
                            </div>
                            <div class="w-full max-w-xl space-y-3">
                                <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="grid gap-3">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="name" value="{{ $category->name }}" class="block w-full rounded-[1.25rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]" required>
                                    <textarea name="description" rows="3" class="block w-full rounded-[1.25rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 text-sm shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]">{{ $category->description }}</textarea>
                                    <div class="flex justify-end">
                                        <button type="submit" class="rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-white">
                                            Mettre a jour
                                        </button>
                                    </div>
                                </form>
                                <div class="flex justify-end">
                                    <form method="POST" action="{{ route('admin.categories.delete', $category) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-full bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="glass-panel rounded-[2rem] border-dashed p-8 text-center text-sm text-stone-500">
                        Aucune categorie disponible.
                    </div>
                @endforelse
            </section>

            <div>
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
