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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="ambient-page social-page antialiased">
        <div class="page-shell">
            <div class="mx-auto max-w-[1380px] px-0 sm:px-4 lg:px-6">
                <div class="app-frame lg:grid lg:grid-cols-[17rem_minmax(0,1fr)] lg:gap-8">
                    <aside class="app-sidebar lg:sticky lg:top-0 lg:h-screen lg:py-3">
                        @include('layouts.navigation')
                    </aside>

                    <div class="app-main pb-14 lg:py-3">
                        @isset($header)
                            <header class="page-header-shell x-shell-divider pt-0">
                                <div class="app-header-card px-5 py-5 sm:px-6">
                                    {{ $header }}
                                </div>
                            </header>
                        @endisset

                        <main class="page-content">
                            {{ $slot }}
                        </main>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
