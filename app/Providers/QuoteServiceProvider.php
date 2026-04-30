<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class QuoteServiceProvider extends ServiceProvider
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
            // Array of quotes
            $quotes = [
                "Success is not final, failure is not fatal: It is the courage to continue that counts.",
                "Hard work beats talent when talent doesn`t work hard.",
                "Success usually comes to those who are too busy to be looking for it.",
                "Don`t wish it were easier; wish you were better.",
                "Success is walking from failure to failure with no loss of enthusiasm.",
                "Nothing in life is to be feared, it is only to be understood. Now is the time to understand more, so that we may fear less. - Marie Curie"
            ];
    
            // Pick a random quote
            $randomQuote = $quotes[array_rand($quotes)];
    
            // Share the quote with all views
            $view->with('randomQuote', $randomQuote);
        });    
    }
}
