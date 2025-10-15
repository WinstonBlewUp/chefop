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
        View::composer('*', function ($view) {
            $menuLinks = MenuLink::with(['page', 'category'])->orderBy('order')->get();
            
            // Exclure les catégories spéciales du menu
            $menuLinks = $menuLinks->filter(function ($link) {
                if ($link->page_id) {
                    return true; // Garder toutes les pages
                }
                // Exclure Selected Work et Stills des catégories du menu
                return !in_array($link->slug, ['selected-work', 'stills']);
            });
            
            $view->with('menuLinks', $menuLinks);
        });    
    }
}
