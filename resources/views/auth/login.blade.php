<x-guest-layout>
    <div class="mb-8">
        <p class="section-kicker">Connexion</p>
        <h1 class="mt-3 text-4xl font-semibold text-stone-950">Heureux de te revoir</h1>
        <p class="muted-copy mt-3 text-sm leading-7">
            Connecte-toi pour reprendre les discussions, publier un sujet ou repondre aux derniers messages.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Mot de passe" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="rounded-[1.5rem] bg-white/60 px-4 py-3">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-[rgba(90,60,40,0.18)] text-[var(--brand)] shadow-sm focus:ring-[var(--brand)]" name="remember">
                <span class="ms-2 text-sm text-stone-600">Se souvenir de moi</span>
            </label>
        </div>

        <div class="flex flex-col gap-4 pt-2 sm:flex-row sm:items-center sm:justify-between">
            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-stone-600 underline underline-offset-4 transition hover:text-stone-900 focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:ring-offset-2" href="{{ route('password.request') }}">
                    Mot de passe oublie ?
                </a>
            @endif

            <x-primary-button class="justify-center sm:ms-3">
                Connexion
            </x-primary-button>
        </div>
    </form>

    <div class="mt-8 rounded-[1.75rem] bg-[linear-gradient(135deg,var(--brand),var(--accent-soft))] px-5 py-4 text-white shadow-[0_18px_35px_rgba(79,70,229,0.22)]">
        <p class="text-sm font-medium text-white/75">Nouveau ici ?</p>
        <p class="mt-2 text-lg font-semibold">Cree un compte pour participer aux discussions.</p>
        <a href="{{ route('register') }}" class="mt-3 inline-flex text-sm font-semibold underline underline-offset-4">
            Aller a l'inscription
        </a>
    </div>
</x-guest-layout>
