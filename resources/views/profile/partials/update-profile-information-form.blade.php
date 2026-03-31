<section>
    <header>
        <p class="section-kicker">Section 01</p>
        <h2 class="mt-3 text-3xl font-semibold text-stone-950">
            Informations du profil
        </h2>

        <p class="muted-copy mt-3 text-sm leading-7">
            Mets a jour les informations de ton compte et ton adresse e-mail.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-8 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nom" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="mt-2 text-sm text-stone-800">
                        Ton adresse e-mail n'est pas verifiee.

                        <button form="send-verification" class="rounded-md text-sm font-medium text-stone-600 underline underline-offset-4 hover:text-stone-900 focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:ring-offset-2">
                            Clique ici pour renvoyer l'e-mail de verification.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-3 rounded-[1.25rem] border border-emerald-200 bg-emerald-50/90 px-4 py-3 font-medium text-sm text-emerald-700">
                            Un nouveau lien de verification a ete envoye a ton adresse e-mail.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-primary-button>Enregistrer</x-primary-button>

            @if (session('status') === 'profile-updated')
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
