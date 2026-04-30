<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class WeatherServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            // $weather = Cache::remember('weather_bangalore', 3600, function () {
            //     $response = Http::get('https://wttr.in/Bangalore?format=3');
            //     return $response->body();
            // });
            $weather = '';

            $ip = request()->ip();
        
            $view->with(['weather' => $weather, 'ip' => $ip]);
        });
    }
}
