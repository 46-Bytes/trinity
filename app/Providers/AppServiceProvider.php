<?php

namespace App\Providers;

use App\Services\GPTService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    const HOME = '/dashboard';

    /**
     * Register any application services.
     */
    public function register(): void {
        $this->app->singleton(GPTService::class, function ($app) {
            return new GPTService(); // Adjust as needed
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        // Define your admin gate right here:
        Gate::define('run-commands', fn($user) => $user->isAdmin());
    }
}
