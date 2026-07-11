<?php

namespace App\Providers;

use App\Models\ConfigGeneral;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
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
        View::composer('*', function ($view) {
            $configGeneral = Cache::remember('config_general', now()->addHours(1), function () {
                return ConfigGeneral::first();
            });

            $view->with('configGeneral', $configGeneral);
        });
    }
}
