<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'RUTX') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-[#f4f6f8] select-none h-screen relative">
        <div class="min-h-screen flex flex-col justify-center items-center py-6 px-4">
            
            <!-- Login Container Card -->
            <div class="w-full max-w-[440px] bg-white rounded-xl border border-gray-200 shadow-[0_4px_20px_-2px_rgba(0,0,0,0.05)] p-8">
                <!-- RUTX Centered Text Logo -->
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-extrabold text-[#004066] tracking-wider">RUTX</h1>
                </div>

                {{ $slot }}
            </div>

            <!-- Cookie Pill on Bottom Left -->
            <div class="absolute bottom-6 left-6">
                <button class="bg-[#24292f] hover:bg-[#1a1e22] text-[10px] font-semibold text-gray-300 py-2 px-4 rounded-md transition duration-150 shadow cursor-pointer">
                    Gestionar las cookies o rechazarlas
                </button>
            </div>
        </div>
    </body>
</html>

