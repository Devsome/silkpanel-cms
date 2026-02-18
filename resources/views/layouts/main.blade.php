<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SilkPanel CMS') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
    @include('layouts.navigation')

    <main class="min-h-screen bg-white dark:bg-gray-900">
        {{ $slot }}
    </main>
</body>

</html>
