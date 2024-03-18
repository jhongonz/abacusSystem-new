<?php

namespace App\Providers;

use App\View\Composers\HomeComposer;
use App\View\Composers\MenuComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
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
        View::composer('layouts.home', HomeComposer::class);

        View::composer('*', function($view) {
            $isAjax = request()->ajax();

            if (!$isAjax) {
                View::composer('layouts.menu', MenuComposer::class);
            }

            $baseHome = (!$isAjax) ? 'layouts.home' : 'layouts.home-ajax';
            $view->with('layout', $baseHome);
        });
    }
}
