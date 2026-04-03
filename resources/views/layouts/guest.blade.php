@props(['metaDescription' => 'Sphere'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $metaDescription }}">

        <title>{{ config('app.name', 'Sphere') }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <script>
            (() => {
                try {
                    const storedTheme = localStorage.getItem('sphere-theme');
                    document.documentElement.dataset.theme = storedTheme === 'dark' ? 'dark' : 'light';
                } catch (error) {
                    document.documentElement.dataset.theme = 'light';
                }
            })();
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="ambient-page social-page text-gray-900 antialiased" x-data>
        <div class="page-shell flex min-h-screen flex-col items-center justify-center px-4 py-10 sm:px-6">
            <div class="fixed right-4 top-4 z-[95] sm:right-6 sm:top-6">
                <button
                    type="button"
                    @click="$store.theme.toggle()"
                    class="app-theme-fab inline-flex items-center justify-center rounded-full border transition"
                    :aria-label="$store.theme.mode === 'dark' ? 'Activer le mode clair' : 'Activer le mode nuit'"
                    :title="$store.theme.mode === 'dark' ? 'Mode clair' : 'Mode nuit'"
                >
                    <svg x-show="$store.theme.mode !== 'dark'" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z" fill="currentColor"/>
                    </svg>
                    <svg x-show="$store.theme.mode === 'dark'" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 4V2m0 20v-2m8-8h2M2 12h2m13.657 5.657 1.414 1.414M4.929 4.929l1.414 1.414m11.314-1.414-1.414 1.414M6.343 17.657l-1.414 1.414M12 17a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
            <div>
                <a href="{{ route('topics.index') }}" class="inline-flex items-center gap-3 text-2xl font-semibold tracking-tight text-gray-900">
                    <span class="brand-dot"></span>
                    {{ config('app.name', 'Sphere') }}
                </a>
            </div>

            <div class="glass-panel-strong mt-6 w-full overflow-hidden rounded-[2rem] px-6 py-6 sm:max-w-md sm:px-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
