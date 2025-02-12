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
        $this->app->tag([
            CreateEmployeeActionExecutor::class,
            UpdateEmployeeActionExecutor::class,
        ], 'employee.action-executor');
    }

    /**
     * Bootstrap services.
     *
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        /** @var ActionExecutorHandlerContract $actionHandler */
        $actionHandler = $this->app->make(ActionExecutorHandlerContract::class);

        foreach ($this->app->tagged('employee.action-executor') as $executor) {
            $actionHandler->addActionExecutor($executor);
        }
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
