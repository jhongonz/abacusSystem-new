<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-09 17:14:35
 */

namespace App\Http\Controllers\ActionExecutors\InstitutionActions;

use App\Http\Orchestrators\OrchestratorHandlerContract;
use Core\Institution\Domain\Institution;
use Illuminate\Http\Request;

class CreateInstitutionActionExecutor extends InstitutionActionExecutor
{
    public function __construct(
        OrchestratorHandlerContract $orchestratorHandler
    ) {
        parent::__construct($orchestratorHandler);
    }

    /**
     * @param Request $request
     * @return Institution
     */
    public function invoke(Request $request): Institution
    {
        return $this->orchestratorHandler->handler('create-institution', $request);
    }

    /**
     * @return string
     */
    public function canExecute(): string
    {
        return 'create-institution-action';
    }
}
