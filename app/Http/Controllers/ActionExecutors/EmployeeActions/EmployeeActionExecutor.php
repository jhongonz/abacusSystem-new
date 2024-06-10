<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-09 21:45:05
 */

namespace App\Http\Controllers\ActionExecutors\EmployeeActions;

use App\Http\Controllers\ActionExecutors\ActionExecutor;
use App\Http\Orchestrators\OrchestratorHandlerContract;

/**
 * @codeCoverageIgnore
 */
abstract class EmployeeActionExecutor implements ActionExecutor
{
    protected OrchestratorHandlerContract $orchestratorHandler;

    public function __construct(
        OrchestratorHandlerContract $orchestratorHandler,
    ) {
        $this->orchestratorHandler = $orchestratorHandler;
    }
}
