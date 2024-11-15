<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 12:11:33
 */

namespace App\Http\Orchestrators;

use App\Http\Orchestrators\Orchestrator\Orchestrator;
use Illuminate\Http\Request;

interface OrchestratorHandlerContract
{
    /**
     * @param string $actionType
     * @param Request $request
     *
     * @return array<int|string, mixed>|bool
     */
    public function handler(string $actionType, Request $request): array|bool;

    /**
     * @param Orchestrator $orchestrator
     * @return OrchestratorHandlerContract
     */
    public function addOrchestrator(Orchestrator $orchestrator): OrchestratorHandlerContract;
}
