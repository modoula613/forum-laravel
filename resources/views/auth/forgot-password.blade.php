<x-guest-layout>
    <div class="mb-8">
        <p class="section-kicker">Recuperation</p>
        <h1 class="mt-3 text-4xl font-semibold text-stone-950">Mot de passe oublie ?</h1>
        <p class="muted-copy mt-3 text-sm leading-7">
            Indique ton adresse e-mail et nous t'enverrons un lien pour reinitialiser ton acces.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex justify-end pt-2">
            <x-primary-button>
                Envoyer le lien de reinitialisation
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
