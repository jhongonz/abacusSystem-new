<?php

namespace App\Providers;

use App\View\Composers\HomeComposer;
use App\View\Composers\MenuComposer;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    private Session $session;
    private ViewFactory $viewFactory;

    /**
     * @throws BindingResolutionException
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->session = $this->app->make(Session::class);
        $this->viewFactory = $this->app->make(ViewFactory::class);
    }

    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->viewFactory->composer('layouts.home', HomeComposer::class);

        $this->viewFactory->composer('*', function ($view) {
            /** @var Request $requestService */
            $requestService = $this->app->make(Request::class);

            $isAjax = $requestService->ajax();
            if (null !== $this->session->get('user') && !$isAjax) {
                $this->viewFactory->composer('layouts.menu', MenuComposer::class);
            }

            $baseHome = (!$isAjax) ? 'layouts.home' : 'layouts.home-ajax';
            $view->with('layout', $baseHome);
        });
    }
}
