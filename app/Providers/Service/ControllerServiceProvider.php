<?php

namespace App\Providers\Service;

use App\Http\Controllers\ActionExecutors\ActionExecutorHandler;
use App\Http\Controllers\ActionExecutors\ActionExecutorHandlerContract;
use App\Http\Controllers\ActionExecutors\EmployeeActions\CreateEmployeeActionExecutor;
use App\Http\Controllers\ActionExecutors\EmployeeActions\UpdateEmployeeActionExecutor;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class ControllerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singletonIf(ActionExecutorHandlerContract::class, function (Application $app) {
            return new ActionExecutorHandler();
        });
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
}
