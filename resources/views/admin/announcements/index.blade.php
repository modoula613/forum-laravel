<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl">
            <p class="section-kicker">Administration</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Gestion des annonces</h2>
            <p class="muted-copy mt-3 text-base leading-7">
                Suis les annonces globales du forum et controle leur visibilite depuis cet espace.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="glass-panel rounded-[1.5rem] border-emerald-200 bg-emerald-50/90 px-5 py-4 text-sm text-emerald-900">
                    {{ session('success') }}
                </div>
            @endif

            <section class="glass-panel-strong rounded-[2rem] p-6">
                <p class="section-kicker">Nouvelle annonce</p>
                <h3 class="mt-3 text-3xl font-semibold text-stone-950">Publier une annonce globale</h3>
                <form method="POST" action="{{ route('admin.announcements.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="title" :value="__('Titre')" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>
                    <div>
                        <x-input-label for="content" :value="__('Contenu')" />
                        <textarea
                            id="content"
                            name="content"
                            rows="5"
                            class="mt-1 block w-full rounded-[1.5rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-4 shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                            required
                        >{{ old('content') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('content')" />
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>Publier</x-primary-button>
                    </div>
                </form>
            </section>

            <div class="space-y-4">
                @forelse ($announcements as $announcement)
                    <article class="glass-panel rounded-[2rem] p-6">
                        <div class="grid gap-6 lg:grid-cols-[1fr_auto]">
                            <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" class="space-y-4">
                                @csrf
                                @method('PUT')
                                <div class="flex flex-wrap items-center gap-3">
                                    <h3 class="text-2xl font-semibold text-stone-950">Edition</h3>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] {{ $announcement->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-200 text-stone-600' }}">
                                        {{ $announcement->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div>
                                    <x-input-label :value="__('Titre')" />
                                    <x-text-input name="title" type="text" class="mt-1 block w-full" :value="$announcement->title" required />
                                </div>
                                <div>
                                    <x-input-label :value="__('Contenu')" />
                                    <textarea
                                        name="content"
                                        rows="5"
                                        class="mt-1 block w-full rounded-[1.5rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-4 shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                                        required
                                    >{{ $announcement->content }}</textarea>
                                </div>
                                <label class="inline-flex items-center gap-3 text-sm text-stone-600">
                                    <input type="checkbox" name="is_active" value="1" @checked($announcement->is_active) class="rounded border-stone-300 text-[var(--brand)] focus:ring-[var(--brand)]">
                                    Active
                                </label>
                                <p class="text-xs uppercase tracking-[0.16em] text-stone-500">
                                    Creee le {{ $announcement->created_at->format('d/m/Y H:i') }}
                                </p>
                                <div class="flex flex-wrap gap-3">
                                    <x-primary-button type="submit">Mettre a jour</x-primary-button>
                                </div>
                            </form>

                            <div class="flex flex-col gap-3">
                                <form method="POST" action="{{ route('admin.announcements.toggle', $announcement) }}">
                                    @csrf
                                    @method('PATCH')
                                    <x-secondary-button type="submit">
                                        {{ $announcement->is_active ? 'Desactiver' : 'Activer' }}
                                    </x-secondary-button>
                                </form>
                                <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button type="submit">
                                        Supprimer
                                    </x-danger-button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="glass-panel rounded-[2rem] border-dashed p-8 text-center text-sm text-stone-500">
                        Aucune annonce disponible pour le moment.
                    </div>
                @endforelse
            </div>

            <div class="glass-panel rounded-[2rem] px-4 py-4 sm:px-6">
                {{ $announcements->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
