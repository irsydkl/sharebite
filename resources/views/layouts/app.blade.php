<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - ShareBite</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    @stack('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&display=swap');
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8fafc;
        }
    </style>
</head>

<body class="flex h-screen bg-[#f8f9fa] text-gray-800 font-sans antialiased overflow-hidden">
    @yield('content')
    <script src="https://kit.fontawesome.com/05aea6e721.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    @stack('body-scripts')
</body>

</html>
