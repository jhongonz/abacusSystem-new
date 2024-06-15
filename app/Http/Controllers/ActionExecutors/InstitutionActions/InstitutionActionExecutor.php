<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-09 17:12:30
 */

namespace App\Http\Controllers\ActionExecutors\InstitutionActions;

use App\Http\Controllers\ActionExecutors\ActionExecutor;
use App\Http\Orchestrators\OrchestratorHandlerContract;

/**
 * @codeCoverageIgnore
 */
abstract class InstitutionActionExecutor implements ActionExecutor
{
    protected OrchestratorHandlerContract $orchestratorHandler;
    public function __construct(
        OrchestratorHandlerContract $orchestratorHandler
    ) {
        $this->orchestratorHandler = $orchestratorHandler;
    }
}
