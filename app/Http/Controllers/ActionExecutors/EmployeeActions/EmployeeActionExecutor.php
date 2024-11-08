<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-09 21:45:05
 */

namespace App\Http\Controllers\ActionExecutors\EmployeeActions;

use App\Http\Controllers\ActionExecutors\ActionExecutor;
use App\Http\Orchestrators\OrchestratorHandlerContract;

abstract class EmployeeActionExecutor implements ActionExecutor
{
    public function __construct(
        protected readonly OrchestratorHandlerContract $orchestratorHandler,
    ) {
    }
}
