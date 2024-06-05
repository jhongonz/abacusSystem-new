<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 17:42:08
 */

namespace App\Http\Orchestrators\Orchestrator\Module;

use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Module;
use Illuminate\Http\Request;

class DetailModuleOrchestrator extends ModuleOrchestrator
{
    public function __construct(
        ModuleManagementContract $moduleManagement
    ) {
        parent::__construct($moduleManagement);
    }

    /**
     * @inheritDoc
     */
    public function make(Request $request): array
    {
        $moduleId = $request->input('moduleId');
        if (! is_null($moduleId)) {

            /** @var Module $module */
            $module = $this->moduleManagement->searchModuleById($moduleId);
        }

        return [
            'moduleId' => $moduleId,
            'module' => $module ?? null,
            'menuKeys' => config('menu.options')
        ];
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'detail-module';
    }
}
