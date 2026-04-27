<x-guest-layout>
    @php
        $resetEmail = old('email', $request->email);
    @endphp

    <div class="mb-8">
        <p class="section-kicker">Reinitialisation</p>
        <h1 class="mt-3 text-4xl font-semibold text-stone-950">Choisis un nouveau mot de passe</h1>
        <p class="muted-copy mt-3 text-sm leading-7">
            Definis un nouveau mot de passe pour securiser ton compte avant de reprendre les discussions.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        @if ($resetEmail)
            <input type="hidden" name="email" value="{{ $resetEmail }}">

            <div class="rounded-[1.25rem] border border-[var(--line)] bg-[var(--surface-soft)]/70 px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-stone-500">Adresse associee</p>
                <p class="mt-2 text-sm font-medium text-stone-900">{{ $resetEmail }}</p>
            </div>
        @else
            <div>
                <x-input-label for="email" value="Adresse e-mail" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="$resetEmail" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
        @endif

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <p class="mt-2 text-xs leading-6 text-stone-500">
                Minimum 8 caracteres, avec une minuscule, une majuscule, un chiffre et un caractere special. Les balises type <code>&lt;script&gt;</code> ou <code>&lt;?php</code> sont refusees.
            </p>
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

        <div class="flex justify-end pt-2">
            <x-primary-button>
                Reinitialiser le mot de passe
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
