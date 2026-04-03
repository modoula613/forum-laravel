<section>
    <header>
        <p class="section-kicker">Section 02</p>
        <h2 class="mt-3 text-3xl font-semibold text-stone-950">
            Mettre a jour le mot de passe
        </h2>

        <p class="muted-copy mt-3 text-sm leading-7">
            Utilise un mot de passe long et difficile a deviner pour mieux securiser ton compte.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-8 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="Mot de passe actuel" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Nouveau mot de passe" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <p class="mt-2 text-xs leading-6 text-stone-500">
                Minimum 8 caracteres, avec une minuscule, une majuscule, un chiffre et un caractere special. Les balises type <code>&lt;script&gt;</code> ou <code>&lt;?php</code> sont refusees.
            </p>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Confirmer le mot de passe" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-primary-button>Enregistrer</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-stone-600"
                >Enregistre.</p>
            @endif
        </div>
    </form>
</section>
