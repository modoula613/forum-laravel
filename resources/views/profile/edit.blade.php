<x-app-layout>
    <x-slot name="header">
        <div class="max-w-2xl">
            <p class="section-kicker">Compte</p>
            <h2 class="mt-3 text-4xl font-semibold text-stone-950">Profil</h2>
            <p class="muted-copy mt-3 text-base leading-7">
                Mets a jour tes informations, renforce la securite du compte et garde le controle sur tes donnees.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
                <aside class="glass-panel rounded-[2.25rem] p-6 sm:p-7">
                    <p class="section-kicker">Vue d'ensemble</p>
                    <h3 class="mt-3 text-3xl font-semibold text-stone-950">Ton espace personnel</h3>
                    <p class="muted-copy mt-4 text-base leading-8">
                        Chaque section est separee pour que les actions sensibles restent claires: informations publiques, mot de passe et suppression definitive.
                    </p>

                    <div class="mt-8 space-y-4">
                        <div class="rounded-[1.5rem] bg-white/70 p-5">
                            <p class="text-sm font-medium text-stone-500">Informations</p>
                            <p class="mt-2 text-xl font-semibold text-stone-950">Nom et adresse e-mail</p>
                        </div>
                        <div class="rounded-[1.5rem] bg-white/70 p-5">
                            <p class="text-sm font-medium text-stone-500">Securite</p>
                            <p class="mt-2 text-xl font-semibold text-stone-950">Mot de passe et acces</p>
                        </div>
                        <div class="rounded-[1.5rem] bg-[linear-gradient(135deg,var(--brand),var(--accent-soft))] p-5 text-white shadow-[0_18px_35px_rgba(79,70,229,0.22)]">
                            <p class="text-sm font-medium text-white/75">Controle</p>
                            <p class="mt-2 text-xl font-semibold">Suppression du compte disponible si necessaire.</p>
                        </div>
                    </div>
                </aside>

                <div class="space-y-6">
                    <div class="glass-panel-strong p-4 sm:p-8 sm:rounded-[2.25rem] rounded-[2rem]">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div class="glass-panel-strong p-4 sm:p-8 sm:rounded-[2.25rem] rounded-[2rem]">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <div class="glass-panel p-4 sm:p-8 sm:rounded-[2.25rem] rounded-[2rem]">
                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
