<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-16 21:54:50
 */

return [
    /*
    |----------------------------------------------------------------------------------
    | Orchestrators
    |----------------------------------------------------------------------------------
    |
    | Here you may define all the orchestrators for the applications
    |
    */

    // ------------- Employee Orchestrators
    App\Http\Orchestrators\Orchestrator\Employee\CreateEmployeeOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Employee\ChangeStateEmployeeOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Employee\GetEmployeeOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Employee\GetEmployeesOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Employee\DetailEmployeeOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Employee\UpdateEmployeeOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Employee\DeleteEmployeeOrchestrator::class,

    // ------------- Users Orchestrators
    App\Http\Orchestrators\Orchestrator\User\ChangeStateUserOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\User\GetUserOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\User\CreateUserOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\User\UpdateUserOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\User\DeleteUserOrchestrator::class,

    // ------------- Profile Orchestrators
    App\Http\Orchestrators\Orchestrator\Profile\GetProfilesOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Profile\GetProfileOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Profile\ChangeStateProfileOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Profile\DeleteProfileOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Profile\DetailProfileOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Profile\CreateProfileOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Profile\UpdateProfileOrchestrator::class,

    // ------------- Institution Orchestrators
    App\Http\Orchestrators\Orchestrator\Institution\ChangeStateInstitutionOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Institution\GetInstitutionsOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Institution\DetailInstitutionOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Institution\CreateInstitutionOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Institution\UpdateInstitutionOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Institution\DeleteInstitutionOrchestrator::class,

    // ------------- Module Orchestrators
    App\Http\Orchestrators\Orchestrator\Module\ChangeStateModuleOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Module\DetailModuleOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Module\CreateModuleOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Module\UpdateModuleOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Module\DeleteModuleOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Module\GetModulesOrchestrator::class,

    // ------------- Module Orchestrators
    App\Http\Orchestrators\Orchestrator\Campus\GetCampusCollectionOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Campus\DetailCampusOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Campus\CreateCampusOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Campus\UpdateCampusOrchestrator::class,
    App\Http\Orchestrators\Orchestrator\Campus\DeleteCampusOrchestrator::class,
];
