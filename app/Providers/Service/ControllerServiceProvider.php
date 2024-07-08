<?php

namespace App\Providers\Service;

use App\Http\Controllers\ActionExecutors\ActionExecutorHandler;
use App\Http\Controllers\ActionExecutors\EmployeeActions\CreateEmployeeActionExecutor;
use App\Http\Controllers\ActionExecutors\EmployeeActions\UpdateEmployeeActionExecutor;
use App\Http\Controllers\ActionExecutors\InstitutionActions\CreateInstitutionActionExecutor;
use App\Http\Controllers\ActionExecutors\InstitutionActions\UpdateInstitutionActionExecutor;
use App\Http\Controllers\ActionExecutors\ModuleActions\CreateModuleActionExecutor;
use App\Http\Controllers\ActionExecutors\ModuleActions\UpdateModuleActionExecutor;
use App\Http\Controllers\ActionExecutors\ProfileActions\CreateProfileActionExecutor;
use App\Http\Controllers\ActionExecutors\ProfileActions\UpdateProfileActionExecutor;
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
            $actionExecutorHandler = new ActionExecutorHandler;

            $actionExecutorHandler->addActionExecutor(
                $app->make(CreateProfileActionExecutor::class)
            );

            $actionExecutorHandler->addActionExecutor(
                $app->make(UpdateProfileActionExecutor::class)
            );

            $actionExecutorHandler->addActionExecutor(
                $app->make(CreateModuleActionExecutor::class)
            );

            $actionExecutorHandler->addActionExecutor(
                $app->make(UpdateModuleActionExecutor::class)
            );

            $actionExecutorHandler->addActionExecutor(
                $app->make(CreateInstitutionActionExecutor::class)
            );

            $actionExecutorHandler->addActionExecutor(
                $app->make(UpdateInstitutionActionExecutor::class)
            );

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
