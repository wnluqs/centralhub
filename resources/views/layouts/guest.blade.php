<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom style for the background image -->
    <style>
        .bg-left {
            background-image: url('/images/test1.png'); /* ← use forward slash here */
            background-size: cover;
            background-position: center;
        }
    </style>
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="flex min-h-screen w-full">
        <!-- Background Left Side (75%) -->
        <div class="w-3/4 bg-left"></div>

        <!-- Right Login Card (25%) -->
        <div class="w-1/4 flex items-center justify-center bg-white bg-opacity-90 shadow-lg">
            <div class="w-full max-w-sm p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>

</html>
