<x-guest-layout>
    <div class="mb-8">
        <p class="section-kicker">Inscription</p>
        <h1 class="mt-3 text-4xl font-semibold text-stone-950">Rejoins la conversation</h1>
        <p class="muted-copy mt-3 text-sm leading-7">
            Cree ton compte pour ouvrir des sujets, suivre les discussions et intervenir avec ton propre profil.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nom" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Mot de passe" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-4 pt-2 sm:flex-row sm:items-center sm:justify-between">
            <a class="text-sm font-medium text-stone-600 underline underline-offset-4 transition hover:text-stone-900 focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:ring-offset-2" href="{{ route('login') }}">
                Deja inscrit ?
            </a>

            <x-primary-button class="justify-center sm:ms-4">
                Inscription
            </x-primary-button>
        </div>
    </form>

    <div class="mt-8 grid gap-3 sm:grid-cols-2">
        <div class="rounded-[1.5rem] bg-white/65 p-4">
            <p class="text-sm font-medium text-stone-500">Publier</p>
            <p class="mt-2 text-lg font-semibold text-stone-950">Cree tes propres sujets.</p>
        </div>
        <div class="rounded-[1.5rem] bg-white/65 p-4">
            <p class="text-sm font-medium text-stone-500">Participer</p>
            <p class="mt-2 text-lg font-semibold text-stone-950">Reponds aux autres membres.</p>
        </div>
    </div>
</x-guest-layout>
