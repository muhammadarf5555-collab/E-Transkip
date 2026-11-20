@props(['card' => true, 'maxWidth' => 'max-w-md'])
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
    <body class="font-sans antialiased text-slate-800 min-h-screen flex flex-col justify-between p-3 sm:p-5 select-none relative overflow-x-hidden" 
          style="background: radial-gradient(circle at 10% 20%, #064e3b 0%, #022c22 45%, #011812 100%); min-height: 100vh;">
        
        <!-- Dekorasi Cahaya Halus Nuansa Hijau Zamrud & Emas Kampus -->
        <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full blur-3xl pointer-events-none" style="background: rgba(16, 185, 129, 0.20); filter: blur(54px);"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full blur-3xl pointer-events-none" style="background: rgba(245, 158, 11, 0.18); filter: blur(54px);"></div>

        <div class="relative z-10 flex-1 flex items-center justify-center w-full my-auto">
            @if ($card)
                <div class="w-full {{ $maxWidth }} my-8">
                    <div class="bg-white rounded-3xl shadow-2xl shadow-black/40 border border-white/20 p-8 sm:p-10">
                        {{ $slot }}
                    </div>
                </div>
            @else
                {{ $slot }}
            @endif
        </div>

        <!-- Footer Ringkas UINSSC -->
        <div class="relative z-10 text-center text-xs shrink-0 py-2 font-medium" style="color: rgba(167, 243, 208, 0.75);">
            &copy; {{ date('Y') }} PUSTIKOM &bull; UIN Siber Syekh Nurjati Cirebon &bull; E-Transkrip
        </div>
    </body>
</html>
