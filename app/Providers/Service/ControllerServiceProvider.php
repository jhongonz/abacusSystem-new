<?php

namespace App\Providers\Service;

use App\Http\Controllers\ActionExecutors\ActionExecutorHandler;
use App\Http\Controllers\ActionExecutors\EmployeeActions\CreateEmployeeActionExecutor;
use App\Http\Controllers\ActionExecutors\EmployeeActions\UpdateEmployeeActionExecutor;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class ControllerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singletonIf(ActionExecutorHandler::class, function (Application $app) {
            $actionExecutorHandler = new ActionExecutorHandler();

            $actionExecutorHandler->addActionExecutor(
                $app->make(CreateEmployeeActionExecutor::class)
            );

            $actionExecutorHandler->addActionExecutor(
                $app->make(UpdateEmployeeActionExecutor::class)
            );

            return $actionExecutorHandler;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
