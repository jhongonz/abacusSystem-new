<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-19 08:23:35
 */

namespace App\Http\Controllers\ActionExecutors\CampusActions;

use App\Http\Controllers\ActionExecutors\ActionExecutor;
use App\Http\Orchestrators\OrchestratorHandlerContract;

abstract class CampusActionExecutor implements ActionExecutor
{
    protected OrchestratorHandlerContract $orchestratorHandler;

    public function __construct(
        OrchestratorHandlerContract $orchestratorHandler
    ) {
        $this->orchestratorHandler = $orchestratorHandler;
    }
}
