<x-guest-layout>
    <div class="mb-8">
        <p class="section-kicker">Confirmation</p>
        <h1 class="mt-3 text-4xl font-semibold text-stone-950">Zone securisee</h1>
        <p class="muted-copy mt-3 text-sm leading-7">
            Pour continuer, confirme ton mot de passe. Cette etape protege les actions sensibles.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Mot de passe" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end pt-2">
            <x-primary-button>
                Confirmer
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
