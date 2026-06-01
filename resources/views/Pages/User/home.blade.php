<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Sharebite') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <a href="{{ route('home') }}" class="text-xl font-bold text-indigo-600">Sharebite</a>
                <nav class="flex items-center gap-4 text-sm">
                    @auth
                        <a href="{{ route(auth()->user()->dashboardRouteName()) }}" class="text-gray-700 hover:text-indigo-600">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-indigo-600">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600">Masuk</a>
                        <a href="{{ route('register') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Daftar</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                    Hemat makanan, hemat dompet
                </h1>
                <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                    Temukan makanan berlebih berkualitas dari donatur terdekat.
                    @guest
                        Daftar sekarang untuk mulai mengklaim makanan.
                    @else
                        Selamat datang, {{ auth()->user()->name }}!
                    @endguest
                </p>
                @guest
                    <div class="mt-8 flex justify-center gap-4">
                        <a href="{{ route('register') }}" class="rounded-md bg-indigo-600 px-6 py-3 text-white font-medium hover:bg-indigo-700">
                            Mulai Daftar
                        </a>
                        <a href="{{ route('login') }}" class="rounded-md border border-gray-300 px-6 py-3 text-gray-700 font-medium hover:bg-gray-100">
                            Masuk
                        </a>
                    </div>
                @endguest
            </div>
        </main>
    </body>
</html>
