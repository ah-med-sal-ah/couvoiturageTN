<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\Authenticate;
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
        // This is a JSON-only API - never attempt to redirect unauthenticated
        // requests to a "login" web route. Let AuthenticationException render
        // as a JSON 401 response instead.
        Authenticate::redirectUsing(fn () => null);
    }
}
