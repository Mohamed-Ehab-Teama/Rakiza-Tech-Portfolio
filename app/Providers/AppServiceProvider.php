<?php

namespace App\Providers;

use App\Models\User;
use App\Support\Settings;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // One Settings object per request, so settings are read from the
        // cache/database only once.
        $this->app->singleton(Settings::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Super Admin skips every permission check: Gate::before runs before
        // all other checks; returning true allows, null means "decide normally".
        Gate::before(function (User $user) {
            return $user->isSuperAdmin() ? true : null;
        });

        // The dashboard uses its own pagination markup.
        Paginator::defaultView('admin.partials.pagination');
        Paginator::defaultSimpleView('admin.partials.pagination');
    }
}
