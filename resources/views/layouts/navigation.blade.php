<nav x-data="{ open: false }" class="relative z-[70] px-4 pt-4 sm:px-6 lg:px-8">
    <!-- Primary Navigation Menu -->
    <div class="glass-panel mx-auto max-w-7xl rounded-[2rem] px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('topics.index') }}" class="inline-flex items-center gap-3 text-lg font-semibold tracking-tight text-gray-900">
                        <span class="brand-dot"></span>
                        {{ config('app.name', 'Sphere') }}
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('topics.index')" :active="request()->routeIs('topics.*')">
                        Acceuil
                    </x-nav-link>
                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                        Membres
                    </x-nav-link>
                    <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                        Categories
                    </x-nav-link>
                    <x-nav-link :href="route('tags.index')" :active="request()->routeIs('tags.*')">
                        Tags
                    </x-nav-link>
                    <x-nav-link :href="route('badges.index')" :active="request()->routeIs('badges.*') || request()->routeIs('users.badges')">
                        Badges
                    </x-nav-link>
                    <x-nav-link :href="route('stats.index')" :active="request()->routeIs('stats.index')">
                        Statistiques
                    </x-nav-link>
                    <x-nav-link :href="route('leaderboard')" :active="request()->routeIs('leaderboard')">
                        Classement
                    </x-nav-link>
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Tableau de bord
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-3 rounded-full border border-[rgba(90,60,40,0.12)] bg-white/60 px-4 py-2 text-sm font-medium leading-4 text-stone-700 transition duration-150 ease-in-out hover:bg-white focus:outline-none">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[var(--brand)] text-xs font-semibold uppercase text-white shadow-[0_10px_24px_rgba(79,70,229,0.28)]">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                                <div>{{ Auth::user()->name }}</div>
                                @if (auth()->user()->unreadNotifications->count() > 0)
                                    <span class="rounded-full bg-rose-500 px-2 py-0.5 text-[0.65rem] font-semibold text-white">
                                        {{ auth()->user()->unreadNotifications->count() }}
                                    </span>
                                @endif
                                @if (auth()->user()->unreadMessages()->count() > 0)
                                    <span class="rounded-full bg-sky-500 px-2 py-0.5 text-[0.65rem] font-semibold text-white">
                                        {{ auth()->user()->unreadMessages()->count() }}
                                    </span>
                                @endif

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('notifications.index')">
                                Notifications
                                @if (auth()->user()->unreadNotifications->count() > 0)
                                    <span class="ms-2 rounded-full bg-rose-100 px-2 py-0.5 text-xs font-semibold text-rose-600">
                                        {{ auth()->user()->unreadNotifications->count() }}
                                    </span>
                                @endif
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('favorites.index')">
                                Mes favoris
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('tags.followed')">
                                Mes tags suivis
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('topics.feed')">
                                Mon flux
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('activity.index')">
                                Activite recente
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('messages.index')">
                                Messages
                                @if (auth()->user()->unreadMessages()->count() > 0)
                                    <span class="ms-2 rounded-full bg-sky-100 px-2 py-0.5 text-xs font-semibold text-sky-600">
                                        {{ auth()->user()->unreadMessages()->count() }}
                                    </span>
                                @endif
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('replies.bookmarks')">
                                Reponses sauvegardees
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('profile.edit')">
                                Profil
                            </x-dropdown-link>
                            @if (auth()->user()->role === 'admin')
                                <x-dropdown-link :href="route('admin.index')">
                                    Admin
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.users.index')">
                                    Administration
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.topics.index')">
                                    Sujets admin
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.replies.index')">
                                    Reponses admin
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.tags.index')">
                                    Tags admin
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.categories.index')">
                                    Categories admin
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.badges.index')">
                                    Badges admin
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.announcements.index')">
                                    Annonces
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.reports.index')">
                                    Signalements
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.logs.index')">
                                    Logs admin
                                </x-dropdown-link>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    Deconnexion
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center gap-4 text-sm">
                        <a href="{{ route('login') }}" class="font-medium text-stone-600 transition hover:text-stone-900">
                            Connexion
                        </a>
                        <a href="{{ route('register') }}" class="rounded-full bg-[var(--brand)] px-4 py-2 font-medium text-white shadow-[0_12px_24px_rgba(79,70,229,0.2)] transition hover:bg-[var(--brand-deep)]">
                            Inscription
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-full p-2 text-stone-500 transition duration-150 ease-in-out hover:bg-white/70 hover:text-stone-800 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t soft-divider sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('topics.index')" :active="request()->routeIs('topics.*')">
                Forum
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                Membres
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                Categories
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('tags.index')" :active="request()->routeIs('tags.*')">
                Tags
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('badges.index')" :active="request()->routeIs('badges.*') || request()->routeIs('users.badges')">
                Badges
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('stats.index')" :active="request()->routeIs('stats.index')">
                Statistiques
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('leaderboard')" :active="request()->routeIs('leaderboard')">
                Classement
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Tableau de bord
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t soft-divider">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('notifications.index')">
                        Notifications
                        @if (auth()->user()->unreadNotifications->count() > 0)
                            ({{ auth()->user()->unreadNotifications->count() }})
                        @endif
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('favorites.index')">
                        Mes favoris
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('tags.followed')">
                        Mes tags suivis
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('topics.feed')">
                        Mon flux
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('activity.index')">
                        Activite recente
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('messages.index')">
                        Messages
                        @if (auth()->user()->unreadMessages()->count() > 0)
                            ({{ auth()->user()->unreadMessages()->count() }})
                        @endif
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('replies.bookmarks')">
                        Reponses sauvegardees
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('profile.edit')">
                        Profil
                    </x-responsive-nav-link>
                    @if (auth()->user()->role === 'admin')
                        <x-responsive-nav-link :href="route('admin.index')">
                            Admin
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.users.index')">
                            Administration
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.topics.index')">
                            Sujets admin
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.replies.index')">
                            Reponses admin
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.tags.index')">
                            Tags admin
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.categories.index')">
                            Categories admin
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.badges.index')">
                            Badges admin
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.announcements.index')">
                            Annonces
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.reports.index')">
                            Signalements
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.logs.index')">
                            Logs admin
                        </x-responsive-nav-link>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            Deconnexion
                        </x-responsive-nav-link>
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
</nav>
