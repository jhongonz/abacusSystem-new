<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-19 08:25:28
 */

namespace App\Http\Controllers\ActionExecutors\CampusActions;

use App\Http\Orchestrators\OrchestratorHandlerContract;
use Core\Campus\Domain\Campus;
use Illuminate\Http\Request;

class CreateCampusActionExecutor extends CampusActionExecutor
{
    public function __construct(
        OrchestratorHandlerContract $orchestratorHandler
    ) {
        parent::__construct($orchestratorHandler);
    }

    /**
     * @param Request $request
     * @return Campus
     */
    public function invoke(Request $request): Campus
    {
        /** @var Campus $campus */
        return $this->orchestratorHandler->handler('create-campus', $request);
    }

    /**
     * @return string
     */
    public function canExecute(): string
    {
        return 'create-campus-action';
    }
}
