<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sharebite - @yield('title')</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&display=swap');
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8fafc;
        }
    </style>
</head>

<body>
    @yield('content')

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    @vite('resources/js/map-picker.js')
</body>

</html>
