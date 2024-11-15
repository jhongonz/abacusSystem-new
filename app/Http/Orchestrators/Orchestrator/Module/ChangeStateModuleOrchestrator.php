<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 17:18:32
 */

namespace App\Http\Orchestrators\Orchestrator\Module;

use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Module;
use Core\Profile\Exceptions\ModuleNotFoundException;
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
     * @return array<string, mixed>
     * @throws ModuleNotFoundException
     */
    public function make(Request $request): array
    {
        $moduleId = $request->integer('moduleId');
        $module = $this->moduleManagement->searchModuleById($moduleId);

        if (is_null($module)) {
            throw new ModuleNotFoundException(sprintf('Module with id %s not found', $moduleId));
        }

        $state = $module->state();
        if ($state->isNew() || $state->isInactivated()) {
            $state->activate();
        } elseif ($state->isActivated()) {
            $state->inactive();
        }

        $dataUpdate['state'] = $state->value();
        $this->moduleManagement->updateModule($moduleId, $dataUpdate);

        return ['module' => $module];
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'change-state-module';
    }
}
