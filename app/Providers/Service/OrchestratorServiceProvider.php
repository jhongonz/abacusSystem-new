<?php

namespace App\Providers\Service;

use App\Http\Orchestrators\Orchestrator\Campus\ChangeStateCampusOrchestrator;
use App\Http\Orchestrators\Orchestrator\Campus\CreateCampusOrchestrator;
use App\Http\Orchestrators\Orchestrator\Campus\DetailCampusOrchestrator;
use App\Http\Orchestrators\Orchestrator\Campus\GetCampusCollectionOrchestrator;
use App\Http\Orchestrators\Orchestrator\Campus\UpdateCampusOrchestrator;
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
use App\Http\Orchestrators\Orchestrator\Module\ChangeStateModuleOrchestrator;
use App\Http\Orchestrators\Orchestrator\Module\CreateModuleOrchestrator;
use App\Http\Orchestrators\Orchestrator\Module\DeleteModuleOrchestrator;
use App\Http\Orchestrators\Orchestrator\Module\DetailModuleOrchestrator;
use App\Http\Orchestrators\Orchestrator\Module\GetModulesOrchestrator;
use App\Http\Orchestrators\Orchestrator\Module\UpdateModuleOrchestrator;
use App\Http\Orchestrators\Orchestrator\Profile\ChangeStateProfileOrchestrator;
use App\Http\Orchestrators\Orchestrator\Profile\CreateProfileOrchestrator;
use App\Http\Orchestrators\Orchestrator\Profile\DeleteProfileOrchestrator;
use App\Http\Orchestrators\Orchestrator\Profile\DetailProfileOrchestrator;
use App\Http\Orchestrators\Orchestrator\Profile\GetProfileOrchestrator;
use App\Http\Orchestrators\Orchestrator\Profile\GetProfilesOrchestrator;
use App\Http\Orchestrators\Orchestrator\Profile\UpdateProfileOrchestrator;
use App\Http\Orchestrators\Orchestrator\User\CreateUserOrchestrator;
use App\Http\Orchestrators\Orchestrator\User\DeleteUserOrchestrator;
use App\Http\Orchestrators\Orchestrator\User\GetUserOrchestrator;
use App\Http\Orchestrators\Orchestrator\User\ChangeStateUserOrchestrator;
use App\Http\Orchestrators\Orchestrator\User\UpdateUserOrchestrator;
use App\Http\Orchestrators\OrchestratorHandler;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class OrchestratorServiceProvider extends ServiceProvider
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

            $orchestratorHandler->addOrchestrator(
                $app->make(ChangeStateProfileOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(DeleteProfileOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(DetailProfileOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(CreateProfileOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(UpdateProfileOrchestrator::class)
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

            //Module Orchestrators
            $orchestratorHandler->addOrchestrator(
                $app->make(ChangeStateModuleOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(DetailModuleOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(CreateModuleOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(UpdateModuleOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(DeleteModuleOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(GetModulesOrchestrator::class)
            );

            //Campus Orchestrators
            $orchestratorHandler->addOrchestrator(
                $app->make(GetCampusCollectionOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(DetailCampusOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(CreateCampusOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(UpdateCampusOrchestrator::class)
            );

            $orchestratorHandler->addOrchestrator(
                $app->make(ChangeStateCampusOrchestrator::class)
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
