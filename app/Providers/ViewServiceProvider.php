<?php

namespace App\Providers;

use App\View\Composers\HomeComposer;
use App\View\Composers\MenuComposer;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    private Session $session;

    public function __construct($app)
    {
        parent::__construct($app);
    }

    /**
     * Register services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap services.
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $this->session = $this->app->make(Session::class);

        View::composer('layouts.home', HomeComposer::class);
        View::composer('*', function ($view) {

            /** @var Request $requestService */
            $requestService = $this->app->make(Request::class);

            $isAjax = $requestService->ajax();
            if ($this->session->get('user') !== null && ! $isAjax) {
                View::composer('layouts.menu', MenuComposer::class);
            }

            $baseHome = (! $isAjax) ? 'layouts.home' : 'layouts.home-ajax';
            $view->with('layout', $baseHome);
        });
    }
}
