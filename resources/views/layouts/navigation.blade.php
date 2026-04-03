@php
    $navUser = auth()->user();
    $unreadNotificationCount = $navUser ? $navUser->unreadNotifications()->count() : 0;
    $unreadMessageCount = $navUser ? $navUser->unreadMessages()->count() : 0;
@endphp

<nav x-data="{ open: false }" class="relative z-[70]">
    <div class="mx-auto max-w-7xl lg:max-w-none">
        <div class="mb-3 flex items-center justify-between border-b border-white/10 bg-black px-4 py-3 text-white lg:hidden">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 text-lg font-bold tracking-tight text-white">
                <span class="x-mark text-2xl">S</span>
                {{ config('app.name', 'Sphere') }}
            </a>

            <div class="-me-2 flex items-center">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-full p-2 text-white/80 transition hover:bg-white/10 hover:text-white focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="hidden lg:block">
            <div class="flex h-screen flex-col justify-between px-3 py-4 text-white">
                <div>
                    <a href="{{ route('home') }}" class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-full text-3xl font-semibold tracking-tight text-white transition hover:bg-white/10">
                        <span class="x-mark">S</span>
                    </a>

                    <div class="space-y-1">
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
                            <x-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')">
                                Notifications
                                @if ($unreadNotificationCount > 0)
                                    <span class="ml-2 rounded-full bg-[var(--brand)] px-2 py-0.5 text-[0.7rem] font-bold text-white">{{ $unreadNotificationCount }}</span>
                                @endif
                            </x-nav-link>
                            <x-nav-link :href="route('messages.index')" :active="request()->routeIs('messages.*')">
                                Messages
                                @if ($unreadMessageCount > 0)
                                    <span class="ml-2 rounded-full bg-white/15 px-2 py-0.5 text-[0.7rem] font-bold text-white">{{ $unreadMessageCount }}</span>
                                @endif
                            </x-nav-link>
                            <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                                Profil
                            </x-nav-link>
                            @if (auth()->user()->role === 'admin')
                                <x-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.*')">
                                    Espace admin
                                </x-nav-link>
                            @endif
                        @else
                            <x-nav-link :href="route('login')" :active="request()->routeIs('login')">
                                Connexion
                            </x-nav-link>
                        @endauth
                    </div>

                    @auth
                        @if (! auth()->user()->is_blocked)
                            <a href="{{ route('topics.create') }}" class="mt-6 inline-flex w-full items-center justify-center rounded-full bg-white px-5 py-3 text-base font-semibold text-black transition hover:bg-white/90">
                                Poster
                            </a>
                        @endif
                    @else
                        <a href="{{ route('register') }}" class="mt-6 inline-flex w-full items-center justify-center rounded-full bg-white px-5 py-3 text-base font-semibold text-black transition hover:bg-white/90">
                            Rejoindre
                        </a>
                    @endauth
                </div>

                @auth
                    <div class="rounded-full p-2 transition hover:bg-white/6">
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-sm font-semibold uppercase text-white">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold text-white">{{ Auth::user()->name }}</p>
                                <p class="truncate text-sm text-white/45">{{ '@'.\Illuminate\Support\Str::slug(Auth::user()->name, '') }}</p>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center gap-2">
                            <a href="{{ route('dashboard') }}" class="rounded-full border border-white/10 px-3 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-white/70 transition hover:bg-white/10 hover:text-white">
                                Mon espace
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="rounded-full border border-white/10 px-3 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-white/70 transition hover:bg-white/10 hover:text-white focus:outline-none"
                                >
                                    Deconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden">
        <div class="border-b border-white/10 bg-black p-3 text-white shadow-[0_20px_45px_rgba(0,0,0,0.22)]">
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

            <div class="mt-4 border-t border-white/10 pt-4">
                @auth
                    <div class="px-3">
                        <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-white/45">{{ Auth::user()->email }}</div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <x-responsive-nav-link :href="route('dashboard')">
                            Mon espace
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('notifications.index')">
                            Notifications
                            @if ($unreadNotificationCount > 0)
                                ({{ $unreadNotificationCount }})
                            @endif
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('messages.index')">
                            Messages
                            @if ($unreadMessageCount > 0)
                                ({{ $unreadMessageCount }})
                            @endif
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('profile.edit')">
                            Profil
                        </x-responsive-nav-link>

                        <form method="POST" action="{{ route('logout') }}" class="px-3 pt-2">
                            @csrf
                            <button
                                type="submit"
                                class="block w-full rounded-full border border-white/10 px-4 py-3 text-left text-base font-semibold text-white transition hover:bg-white/10 focus:outline-none"
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
