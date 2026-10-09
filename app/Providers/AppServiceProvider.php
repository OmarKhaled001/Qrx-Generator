<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
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
        Paginator::defaultView('pagination.qrx');

        // Generous per-IP limit for /x/{code}: venues share one IP behind NAT.
        RateLimiter::for('scans', fn (Request $request) => Limit::perMinute(300)->by($request->ip()));

        // Outside production, fail loudly on N+1 lazy loading and on attributes
        // silently dropped by mass assignment (the old app's admin-plan bug).
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());
    }
}
