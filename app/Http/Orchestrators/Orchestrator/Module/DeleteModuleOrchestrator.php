<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 18:10:24
 */

namespace App\Http\Orchestrators\Orchestrator\Module;

use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Illuminate\Http\Request;

class DeleteModuleOrchestrator extends ModuleOrchestrator
{
    public function __construct(
        ModuleManagementContract $moduleManagement,
    ) {
        parent::__construct($moduleManagement);
    }

    /**
     * @return array<null>
     */
    public function make(Request $request): array
    {
        $this->moduleManagement->deleteModule($request->integer('moduleId'));

        return [];
    }

    public function canOrchestrate(): string
    {
        return 'delete-module';
    }
}
