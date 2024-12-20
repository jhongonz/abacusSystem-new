<?php

namespace App\Providers;

use App\View\Composers\EventAjaxComposer;
use App\View\Composers\HomeComposer;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\ServiceProvider;

class ViewFactoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     *
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $viewFactory = $this->app->make(ViewFactory::class);

        $viewFactory->composer('layouts.home', HomeComposer::class);
        $viewFactory->composer('*', EventAjaxComposer::class);
    }
}
