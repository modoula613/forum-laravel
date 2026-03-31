<x-app-layout>
    <x-slot name="header">
        <div class="max-w-2xl">
            <p class="section-kicker">{{ isset($topic) ? 'Edition' : 'Publication' }}</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">
                {{ isset($topic) ? 'Modifier le sujet' : 'Nouveau sujet' }}
            </h2>
            <p class="muted-copy mt-3 text-base leading-7">
                {{ isset($topic) ? 'Ajuste le titre ou le contenu pour clarifier ton idee.' : 'Pose le contexte, formule ton sujet clairement et lance la conversation.' }}
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[1fr_0.32fr]">
                <div class="glass-panel-strong rounded-[2.25rem] p-6 sm:p-8">
                    @if (auth()->user()->is_blocked && ! isset($topic))
                        <div class="rounded-[1.5rem] border border-rose-200 bg-rose-50/90 px-5 py-4 text-sm font-medium text-rose-800">
                            Votre compte est bloque suite a plusieurs infractions.
                        </div>
                    @else
                        <form
                            method="POST"
                            action="{{ isset($topic) ? route('topics.update', $topic) : route('topics.store') }}"
                            class="space-y-6"
                        >
                            @csrf
                            @isset($topic)
                                @method('PUT')
                            @endisset

                            <div>
                                <x-input-label for="title" :value="__('Titre')" />
                                <x-text-input
                                    id="title"
                                    name="title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="old('title', $topic->title ?? '')"
                                    required
                                    autofocus
                                />
                                <x-input-error class="mt-2" :messages="$errors->get('title')" />
                            </div>

                            <div>
                                <x-input-label for="category_id" :value="__('Categorie')" />
                                <select
                                    id="category_id"
                                    name="category_id"
                                    class="mt-1 block w-full rounded-[1.5rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                                >
                                    <option value="">Aucune categorie</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected((string) old('category_id', $topic->category_id ?? '') === (string) $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                            </div>

                            <div>
                                <x-input-label for="tags" :value="__('Tags')" />
                                <select
                                    id="tags"
                                    name="tags[]"
                                    multiple
                                    class="mt-1 block min-h-36 w-full rounded-[1.5rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-3 shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                                >
                                    @php
                                        $selectedTags = collect(old('tags', isset($topic) ? $topic->tags->pluck('id')->all() : []))->map(fn ($value) => (string) $value)->all();
                                    @endphp
                                    @foreach ($tags as $tag)
                                        <option value="{{ $tag->id }}" @selected(in_array((string) $tag->id, $selectedTags, true))>
                                            {{ $tag->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-2 text-sm text-stone-500">Maintiens `Cmd` ou `Ctrl` pour selectionner plusieurs tags.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('tags')" />
                                <x-input-error class="mt-2" :messages="$errors->get('tags.*')" />
                            </div>

                            <div>
                                <x-input-label for="content" :value="__('Contenu')" />
                                <textarea
                                    id="content"
                                    name="content"
                                    rows="8"
                                    class="mt-1 block w-full rounded-[1.5rem] border-[rgba(71,85,135,0.16)] bg-white/80 px-4 py-4 shadow-sm focus:border-[var(--brand)] focus:ring-[var(--brand)]"
                                    required
                                >{{ old('content', $topic->content ?? '') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('content')" />
                            </div>

                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('topics.index') }}" class="text-sm text-stone-500 hover:text-stone-700">
                                    Annuler
                                </a>
                                <x-secondary-button type="submit" name="save_draft" value="1">
                                    Enregistrer en brouillon
                                </x-secondary-button>
                                <x-primary-button>
                                    {{ isset($topic) ? 'Mettre a jour' : 'Publier' }}
                                </x-primary-button>
                            </div>
                        </form>
                    @endif
                </div>

                <aside class="glass-panel rounded-[2.25rem] p-6">
                    <p class="section-kicker">Conseils</p>
                    <div class="mt-4 space-y-4 text-sm leading-7 text-stone-600">
                        <div class="rounded-[1.5rem] bg-white/70 p-4">
                            <p class="font-semibold uppercase tracking-[0.16em] text-stone-800">Titre</p>
                            <p class="mt-2">Va droit au sujet. Une formulation concrete attire de meilleures reponses.</p>
                        </div>
                        <div class="rounded-[1.5rem] bg-white/70 p-4">
                            <p class="font-semibold uppercase tracking-[0.16em] text-stone-800">Contenu</p>
                            <p class="mt-2">Ajoute le contexte, le probleme ou l'objectif pour lancer une discussion utile.</p>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
