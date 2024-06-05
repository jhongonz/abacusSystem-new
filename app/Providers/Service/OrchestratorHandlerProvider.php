<?php

namespace App\Providers\Service;

use App\Http\Orchestrators\Orchestrator\Employee\CreateEmployeeOrchestrator;
use App\Http\Orchestrators\Orchestrator\Employee\DeleteEmployeeOrchestrator;
use App\Http\Orchestrators\Orchestrator\Employee\DetailEmployeeOrchestrator;
use App\Http\Orchestrators\Orchestrator\Employee\GetEmployeeOrchestrator;
use App\Http\Orchestrators\Orchestrator\Employee\GetEmployeesOrchestrator;
use App\Http\Orchestrators\Orchestrator\Employee\ChangeStateEmployeeOrchestrator;
use App\Http\Orchestrators\Orchestrator\Employee\UpdateEmployeeOrchestrator;
use App\Http\Orchestrators\Orchestrator\Institution\ChangeStateInstitutionOrchestrator;
use App\Http\Orchestrators\Orchestrator\Institution\CreateInstitutionOrchestrator;
use App\Http\Orchestrators\Orchestrator\Institution\DeleteInstitutionOrchestrator;
use App\Http\Orchestrators\Orchestrator\Institution\DetailInstitutionOrchestrator;
use App\Http\Orchestrators\Orchestrator\Institution\GetInstitutionsOrchestrator;
use App\Http\Orchestrators\Orchestrator\Institution\UpdateInstitutionOrchestrator;
use App\Http\Orchestrators\Orchestrator\Profile\GetProfileOrchestrator;
use App\Http\Orchestrators\Orchestrator\Profile\GetProfilesOrchestrator;
use App\Http\Orchestrators\Orchestrator\User\CreateUserOrchestrator;
use App\Http\Orchestrators\Orchestrator\User\DeleteUserOrchestrator;
use App\Http\Orchestrators\Orchestrator\User\GetUserOrchestrator;
use App\Http\Orchestrators\Orchestrator\User\ChangeStateUserOrchestrator;
use App\Http\Orchestrators\Orchestrator\User\UpdateUserOrchestrator;
use App\Http\Orchestrators\OrchestratorHandler;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class OrchestratorHandlerProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singletonIf(OrchestratorHandlerContract::class, function (Application $app) {
            $orchestratorHandler = new OrchestratorHandler;

            //Employee Orchestrators
            $orchestratorHandler->addOrchestrator(
                $app->make(CreateEmployeeOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(ChangeStateEmployeeOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(GetEmployeesOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(GetEmployeeOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(DetailEmployeeOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(UpdateEmployeeOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(DeleteEmployeeOrchestrator::class)
            );

            //User Orchestrators
            $orchestratorHandler->addOrchestrator(
                $app->make(ChangeStateUserOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(GetUserOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(CreateUserOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(UpdateUserOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(DeleteUserOrchestrator::class)
            );

            //Profile Orchestrators
            $orchestratorHandler->addOrchestrator(
                $app->make(GetProfilesOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(GetProfileOrchestrator::class)
            );

            //Institution Orchestrators
            $orchestratorHandler->addOrchestrator(
                $app->make(ChangeStateInstitutionOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(GetInstitutionsOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(DetailInstitutionOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(CreateInstitutionOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(UpdateInstitutionOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(DeleteInstitutionOrchestrator::class)
            );

            return $orchestratorHandler;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
