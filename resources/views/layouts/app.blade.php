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

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|newsreader:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="ambient-page antialiased">
        <div class="page-shell">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="px-4 pt-6 sm:px-6 lg:px-8">
                    <div class="glass-panel mx-auto max-w-7xl rounded-[2rem] px-6 py-7 sm:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="pb-14">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
