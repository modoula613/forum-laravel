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
    <body class="ambient-page social-page text-gray-900 antialiased">
        <div class="page-shell flex min-h-screen flex-col items-center justify-center px-4 py-10 sm:px-6">
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
