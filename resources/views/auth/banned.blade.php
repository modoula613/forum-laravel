<x-guest-layout>
    <div class="space-y-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="section-kicker">Compte suspendu</p>
                <h1 class="mt-3 text-4xl font-semibold text-stone-950">Connexion indisponible</h1>
                <p class="muted-copy mt-3 text-sm leading-7">
                    Ce compte ne peut pas acceder au forum pour le moment.
                </p>
            </div>
            <a
                href="{{ route('home') }}"
                class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 text-xl font-semibold leading-none text-stone-500 transition hover:bg-white hover:text-stone-900"
                aria-label="Quitter la page"
                title="Quitter"
            >
                ×
            </a>
        </div>

        <div class="rounded-[1.75rem] border border-rose-200 bg-rose-50/90 px-5 py-5 text-rose-900">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-rose-700">Acces bloque</p>
            @if ($email)
                <p class="mt-3 text-base leading-7">
                    Le compte <span class="font-semibold">{{ $email }}</span> est actuellement suspendu.
                </p>
            @else
                <p class="mt-3 text-base leading-7">
                    Ce compte est actuellement suspendu.
                </p>
            @endif
            @if ($bannedUntil)
                <p class="mt-2 text-sm leading-7 text-rose-800">
                    Suspension en place jusqu’au {{ \Illuminate\Support\Carbon::parse($bannedUntil)->format('d/m/Y H:i') }}.
                </p>
            @endif
        </div>

        <div class="rounded-[1.75rem] bg-white/70 px-5 py-5 text-stone-700">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-stone-500">Que faire maintenant ?</p>
            <p class="mt-3 text-sm leading-7">
                Si tu penses qu’il s’agit d’une erreur, contacte l’administration du forum. Sinon, tu peux revenir a l’accueil et consulter les contenus publics.
            </p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('home') }}" class="inline-flex items-center rounded-full bg-[var(--brand)] px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-white transition hover:bg-[var(--brand-deep)]">
                    Retour a l’accueil
                </a>
                <a href="{{ route('topics.index') }}" class="inline-flex items-center rounded-full border border-[rgba(71,85,135,0.16)] bg-white/80 px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-stone-700 transition hover:bg-white">
                    Voir le forum
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
