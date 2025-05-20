<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\MenuLink;
use Illuminate\Support\Facades\View;

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
        $menuLinks = MenuLink::with('page')->get();
        View::share('menuLinks', $menuLinks);View::composer('*', function ($view) {
            $menuLinks = MenuLink::with('page')->get();
            $view->with('menuLinks', $menuLinks);
        });    
    }
}
