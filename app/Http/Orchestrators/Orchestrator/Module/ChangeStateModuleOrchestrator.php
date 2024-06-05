<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 17:18:32
 */

namespace App\Http\Orchestrators\Orchestrator\Module;

use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Module;
use Illuminate\Http\Request;

class ChangeStateModuleOrchestrator extends ModuleOrchestrator
{
    public function __construct(
        ModuleManagementContract $moduleManagement
    ) {
        parent::__construct($moduleManagement);
    }

    /**
     * @param Request $request
     * @return Module
     */
    public function make(Request $request): Module
    {
        $moduleId = $request->input('moduleId');

        /** @var Module $module */
        $module = $this->moduleManagement->searchModuleById($moduleId);

        $state = $module->state();
        if ($state->isNew() || $state->isInactivated()) {
            $state->activate();
        } elseif ($state->isActivated()) {
            $state->inactive();
        }

        $dataUpdate['state'] = $state->value();
        return $this->moduleManagement->updateModule($moduleId, $dataUpdate);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'change-state-module';
    }
}
