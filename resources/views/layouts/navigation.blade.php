<nav x-data="{ open: false }" class="relative z-[70]">
    <div class="mx-auto max-w-7xl lg:max-w-none">
        <div class="mb-4 flex items-center justify-between rounded-[1.6rem] border border-[rgba(255,255,255,0.75)] bg-white/90 px-4 py-3 shadow-[0_18px_40px_rgba(15,23,42,0.06)] backdrop-blur-xl lg:hidden">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 text-lg font-bold tracking-tight text-gray-950">
                <span class="brand-dot"></span>
                {{ config('app.name', 'Sphere') }}
            </a>

            <div class="-me-2 flex items-center">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-full p-2 text-stone-500 transition hover:bg-slate-100 hover:text-stone-800 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="hidden lg:block">
            <div class="rounded-[2rem] border border-[rgba(255,255,255,0.78)] bg-white/88 p-4 shadow-[0_22px_55px_rgba(15,23,42,0.06)] backdrop-blur-xl">
                <a href="{{ route('home') }}" class="mb-4 inline-flex items-center gap-3 px-3 py-2 text-xl font-bold tracking-tight text-gray-950">
                    <span class="brand-dot"></span>
                    {{ config('app.name', 'Sphere') }}
                </a>

                <div class="space-y-2">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home') || request()->routeIs('topics.*')">
                        Forum
                    </x-nav-link>
                    <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                        Categories
                    </x-nav-link>
                    <x-nav-link :href="route('news.index')" :active="request()->routeIs('news.*')">
                        Actualites
                    </x-nav-link>
                    @auth
                        @if (auth()->user()->role === 'admin')
                            <x-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.*')">
                                Espace admin
                            </x-nav-link>
                        @endif
                    @endauth
                </div>

                @auth
                    <div class="mt-5 rounded-[1.5rem] border border-[rgba(71,85,135,0.12)] bg-[linear-gradient(180deg,rgba(248,250,252,0.95),rgba(241,245,249,0.9))] p-4">
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-[var(--brand)] text-sm font-semibold uppercase text-white shadow-[0_10px_24px_rgba(29,155,240,0.2)]">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-stone-900">{{ Auth::user()->name }}</p>
                                <p class="truncate text-sm text-stone-500">{{ Auth::user()->email }}</p>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
                            <a href="{{ route('dashboard') }}" class="rounded-2xl bg-white px-4 py-3 font-semibold text-stone-800 transition hover:bg-slate-50">
                                Mon espace
                            </a>
                            <a href="{{ route('profile.edit') }}" class="rounded-2xl bg-white px-4 py-3 font-semibold text-stone-800 transition hover:bg-slate-50">
                                Profil
                            </a>
                            <a href="{{ route('notifications.index') }}" class="rounded-2xl bg-white px-4 py-3 font-semibold text-stone-800 transition hover:bg-slate-50">
                                Notifications
                                @if (auth()->user()->unreadNotifications->count() > 0)
                                    <span class="ml-1 text-rose-600">({{ auth()->user()->unreadNotifications->count() }})</span>
                                @endif
                            </a>
                            <a href="{{ route('messages.index') }}" class="rounded-2xl bg-white px-4 py-3 font-semibold text-stone-800 transition hover:bg-slate-50">
                                Messages
                                @if (auth()->user()->unreadMessages()->count() > 0)
                                    <span class="ml-1 text-sky-600">({{ auth()->user()->unreadMessages()->count() }})</span>
                                @endif
                            </a>
                        </div>

                        @if (auth()->user()->role === 'admin')
                            <div class="mt-4 border-t border-[rgba(71,85,135,0.12)] pt-4">
                                <p class="px-1 text-[0.68rem] font-semibold uppercase tracking-[0.22em] text-stone-400">Administration</p>
                                <div class="mt-3 space-y-2">
                                    <a href="{{ route('admin.reports.index') }}" class="block rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-stone-800 transition hover:bg-slate-50">
                                        Signalements
                                    </a>
                                    <a href="{{ route('admin.users.index') }}" class="block rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-stone-800 transition hover:bg-slate-50">
                                        Utilisateurs
                                    </a>
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('logout') }}" class="mt-4">
                            @csrf
                            <button
                                type="submit"
                                class="block w-full rounded-2xl bg-rose-600 px-4 py-3 text-left text-sm font-semibold text-white transition hover:bg-rose-500 focus:outline-none"
                            >
                                Deconnexion
                            </button>
                        </form>
                    </div>
                @else
                    <div class="mt-5 rounded-[1.5rem] border border-[rgba(71,85,135,0.12)] bg-[linear-gradient(180deg,rgba(248,250,252,0.95),rgba(241,245,249,0.9))] p-4">
                        <p class="text-sm leading-6 text-stone-600">Rejoins le flux pour publier, suivre des sujets et recevoir tes notifications.</p>
                        <div class="mt-4 space-y-2">
                            <a href="{{ route('login') }}" class="block rounded-2xl bg-[var(--brand)] px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-[var(--brand-deep)]">
                                Connexion
                            </a>
                            <a href="{{ route('register') }}" class="block rounded-2xl bg-white px-4 py-3 text-center text-sm font-semibold text-stone-800 transition hover:bg-slate-50">
                                Inscription
                            </a>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden">
        <div class="rounded-[1.75rem] border border-[rgba(255,255,255,0.78)] bg-white/92 p-3 shadow-[0_20px_45px_rgba(15,23,42,0.08)] backdrop-blur-xl">
            <div class="space-y-1">
                <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home') || request()->routeIs('topics.*')">
                    Forum
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                    Categories
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('news.index')" :active="request()->routeIs('news.*')">
                    Actualites
                </x-responsive-nav-link>
                @auth
                    @if (auth()->user()->role === 'admin')
                        <x-responsive-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.*')">
                            Espace admin
                        </x-responsive-nav-link>
                    @endif
                @endauth
            </div>

            <div class="mt-4 border-t soft-divider pt-4">
                @auth
                    <div class="px-3">
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <x-responsive-nav-link :href="route('dashboard')">
                            Mon espace
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('notifications.index')">
                            Notifications
                            @if (auth()->user()->unreadNotifications->count() > 0)
                                ({{ auth()->user()->unreadNotifications->count() }})
                            @endif
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('messages.index')">
                            Messages
                            @if (auth()->user()->unreadMessages()->count() > 0)
                                ({{ auth()->user()->unreadMessages()->count() }})
                            @endif
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('profile.edit')">
                            Profil
                        </x-responsive-nav-link>

                        <form method="POST" action="{{ route('logout') }}" class="px-3 pt-2">
                            @csrf
                            <button
                                type="submit"
                                class="block w-full rounded-2xl bg-rose-600 px-4 py-3 text-left text-base font-semibold text-white transition hover:bg-rose-500 focus:outline-none"
                            >
                                Deconnexion
                            </button>
                        </form>
                    </div>
                @else
                    <div class="space-y-1">
                        <x-responsive-nav-link :href="route('login')">
                            Connexion
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('register')">
                            Inscription
                        </x-responsive-nav-link>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>
