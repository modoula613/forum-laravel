<section class="space-y-6">
    <header>
        <p class="section-kicker">Section 03</p>
        <h2 class="mt-3 text-3xl font-semibold text-stone-950">
            Supprimer le compte
        </h2>

        <p class="muted-copy mt-3 text-sm leading-7">
            Une fois ton compte supprimé, toutes tes donnees seront effacees definitivement. Assure-toi de conserver ce dont tu as besoin avant de continuer.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Supprimer le compte</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-2xl font-semibold text-stone-950">
                Es-tu sur de vouloir supprimer ton compte ?
            </h2>

            <p class="muted-copy mt-3 text-sm leading-7">
                Cette action est irreversible. Saisis ton mot de passe pour confirmer la suppression definitive de ton compte.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Mot de passe" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Mot de passe"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Annuler
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    Supprimer le compte
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
