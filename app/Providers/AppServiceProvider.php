<?php

namespace App\Providers;

use App\Models\Agence;
use App\Policies\AgencyPolicy;
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
        // Register policies
        \Illuminate\Support\Facades\Gate::policy(Agence::class, AgencyPolicy::class);
    }
}
