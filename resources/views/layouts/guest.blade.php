<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Gutta Store') }}</title>

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-black text-white min-h-screen font-sans antialiased gutta-grid">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div>
                <a href="/" class="hover:opacity-85 transition-opacity">
                    <img src="{{ asset('images/logo.png') }}" alt="Gutta" class="h-16 w-auto">
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-8 px-8 py-10 bg-zinc-950 border-2 border-white/10 rounded-none overflow-hidden shadow-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
