<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 12:23:21
 */

namespace App\Http\Orchestrators;

use App\Http\Orchestrators\Exceptions\DuplicateOrchestratorException;
use App\Http\Orchestrators\Orchestrator\Orchestrator;
use Illuminate\Http\Request;

class OrchestratorHandler implements OrchestratorHandlerContract
{
    /** @var Orchestrator[] */
    private array $orchestrators;

    /**
     * @param string $actionType
     * @param Request $request
     * @return mixed
     */
    public function handler(string $actionType, Request $request): mixed
    {
        return $this->orchestrators[$actionType]->make($request);
    }

    /**
     * @throws DuplicateOrchestratorException
     */
    public function addOrchestrator(Orchestrator $orchestrator): OrchestratorHandlerContract
    {
        if (isset($this->orchestrators[$orchestrator->canOrchestrate()])) {
            throw new DuplicateOrchestratorException($orchestrator->canOrchestrate().' orchestrate duplicate');
        }

        $this->orchestrators[$orchestrator->canOrchestrate()] = $orchestrator;
        return $this;
    }
}
