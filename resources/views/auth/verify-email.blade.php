<x-guest-layout>
    <div class="mb-8">
        <p class="section-kicker">Verification</p>
        <h1 class="mt-3 text-4xl font-semibold text-stone-950">Confirme ton adresse e-mail</h1>
        <p class="muted-copy mt-3 text-sm leading-7">
            Merci pour ton inscription. Avant de commencer, valide ton adresse via le lien envoye. Si tu n'as rien recu, nous pouvons t'en renvoyer un.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 rounded-[1.5rem] border border-emerald-200 bg-emerald-50/90 px-4 py-3 font-medium text-sm text-emerald-700">
            Un nouveau lien de verification a ete envoye a l'adresse indiquee lors de l'inscription.
        </div>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    Renvoyer l'e-mail de verification
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="text-sm font-medium text-stone-600 underline underline-offset-4 transition hover:text-stone-900 rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--brand)] focus:ring-offset-2">
                Deconnexion
            </button>
        </form>
    </div>
</x-guest-layout>
