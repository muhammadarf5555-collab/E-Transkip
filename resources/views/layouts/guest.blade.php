<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'E-Transkrip UINSSC') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('images/logo-uinssc.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800 bg-[#F5F7FB] min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md my-8">
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-200/80 p-8 sm:p-10">
                {{ $slot }}
            </div>

            <!-- Footer minimalis -->
            <p class="text-center text-xs text-slate-400 mt-6 font-medium">
                &copy; {{ date('Y') }} UIN Siber Syekh Nurjati Cirebon &bull; E-Transkrip
            </p>
        </div>
    </body>
</html>
