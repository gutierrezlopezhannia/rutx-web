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
    <body class="font-sans antialiased bg-[#f4f6f8] text-[#1f2937] h-screen overflow-hidden">
        <div class="h-screen flex flex-col">
            <!-- Topbar (contains Header, Tabs, User Info & Logout) -->
            <livewire:layout.navigation />

            <!-- Main Workspace: Sidebar + Content -->
            <div class="flex flex-1 overflow-hidden">
                <!-- Sidebar -->
                <livewire:layout.sidebar />


                <!-- Main Content Area -->
                <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
                    @if (isset($header))
                        <header class="bg-white border-b border-gray-200 py-3 px-6">
                            <div class="max-w-7xl mx-auto">
                                {{ $header }}
                            </div>
                        </header>
                    @endif

                    <main class="flex-1 py-6 px-8">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>

