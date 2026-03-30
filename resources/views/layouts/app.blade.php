<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Title --}}
        <title>{{ $title ?? config('app.name', 'Share Your Recipe') }}</title>

        {{-- SEO - meta --}}
        <meta name="description" content="{{ $meta ?? 'Discover and share your favourite recipes' }}">

        {{-- Fonts --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        {{-- Assets / Tailwind + DaisyUI by Vite --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Per-view styles --}}
        @stack('styles')

    </head>
    <body class="font-sans antialiased bg-base-200">
        {{-- Main component (layout + navbar + drawer) --}}
        <x-navigation>
            {{ $slot }}
        </x-navigation>

        {{-- Per-view scripts --}}
        @stack('scripts')

    </body>
</html>
