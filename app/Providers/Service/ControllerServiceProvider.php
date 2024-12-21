<?php

namespace App\Providers\Service;

use App\Http\Controllers\ActionExecutors\ActionExecutorHandler;
use App\Http\Controllers\ActionExecutors\ActionExecutorHandlerContract;
use App\Http\Controllers\ActionExecutors\EmployeeActions\CreateEmployeeActionExecutor;
use App\Http\Controllers\ActionExecutors\EmployeeActions\UpdateEmployeeActionExecutor;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ControllerServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * All the container bindings that should be registered.
     *
     * @var array<string, string>
     */
    public array $singletons = [
        ActionExecutorHandlerContract::class => ActionExecutorHandler::class,
    ];

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
        $actionHandler = $this->app->make(ActionExecutorHandlerContract::class);
        $actionHandler->addActionExecutor($this->app->make(CreateEmployeeActionExecutor::class));
        $actionHandler->addActionExecutor($this->app->make(UpdateEmployeeActionExecutor::class));
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            ActionExecutorHandlerContract::class,
        ];
    }
}
