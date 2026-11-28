<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (
            config('app.env') === 'production'
            || request()->server('HTTP_X_FORWARDED_PROTO') === 'https'
            || request()->header('X-Forwarded-Proto') === 'https'
            || str_contains(request()->header('host', ''), 'pinggy')
            || str_contains(request()->header('host', ''), 'loca.lt')
            || str_contains(request()->header('host', ''), 'ngrok')
            || str_contains(request()->header('host', ''), 'localhost.run')
            || str_contains(request()->header('host', ''), '.lhr.life')
        ) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
