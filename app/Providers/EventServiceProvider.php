<?php

namespace App\Providers;

use Illuminate\Contracts\Config\Repository as Configuration;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    private Configuration $configuration;
    private Dispatcher $dispatcher;

    /**
     * @throws BindingResolutionException
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->configuration = $this->app->make(Configuration::class);
        $this->dispatcher = $this->app->make(Dispatcher::class);
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
        /** @var array<string> $eventListeners */
        $eventListeners = $this->configuration->get('events');

        foreach ($eventListeners as $event => $listener) {
            $this->dispatcher->listen($event, $listener);
        }
    }
}
