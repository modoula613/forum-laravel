<x-guest-layout>
    @php
        $resetEmail = session('reset_email', old('email'));
        $passwordResetSent = session('password_reset_sent', false);
    @endphp

    <div class="mb-8">
        <p class="section-kicker">Recuperation</p>
        <h1 class="mt-3 text-4xl font-semibold text-stone-950">Mot de passe oublie ?</h1>
        <p class="muted-copy mt-3 text-sm leading-7">
            Indique ton adresse e-mail et nous t'enverrons un lien pour reinitialiser ton acces.
        </p>
    </div>

    @if (app()->environment('production') && config('mail.default') === 'log')
        <div class="mb-4 rounded-[1.25rem] border border-amber-200 bg-amber-50/90 px-4 py-3 text-sm font-medium text-amber-800">
            L'envoi d'e-mails n'est pas encore actif sur ce serveur. Le formulaire fonctionne, mais aucun message ne peut etre recu tant qu'un SMTP n'est pas configure.
        </div>
    @endif

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($passwordResetSent && $resetEmail)
        <div class="mb-5 rounded-[1.5rem] border border-emerald-200 bg-emerald-50/95 px-5 py-4 text-sm text-emerald-900">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-700">Verification</p>
            <p class="mt-2 font-semibold">Adresse utilisee : {{ $resetEmail }}</p>
            <p class="mt-2 leading-6 text-emerald-800">
                Le message peut prendre quelques secondes avant d'arriver. Verifie aussi les spams. Inutile de ressaisir ton adresse si tu attends simplement l'e-mail.
            </p>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="$resetEmail" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex justify-end pt-2">
            <x-primary-button>
                {{ $passwordResetSent ? 'Renvoyer le lien de reinitialisation' : 'Envoyer le lien de reinitialisation' }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
