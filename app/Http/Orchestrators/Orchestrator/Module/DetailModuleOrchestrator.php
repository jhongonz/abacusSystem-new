<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 17:42:08
 */

namespace App\Http\Orchestrators\Orchestrator\Module;

use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Illuminate\Config\Repository as Config;
use Illuminate\Http\Request;

class DetailModuleOrchestrator extends ModuleOrchestrator
{
    public function __construct(
        ModuleManagementContract $moduleManagement,
        private readonly Config $config,
    ) {
        parent::__construct($moduleManagement);
    }

    /**
     * @return array<string, mixed>
     */
    public function make(Request $request): array
    {
        $moduleId = $request->integer('moduleId') ?: null;

        if (!is_null($moduleId)) {
            $module = $this->moduleManagement->searchModuleById($moduleId);
        }

        return [
            'moduleId' => $moduleId,
            'module' => $module ?? null,
            'menuKeys' => $this->config->get('menu.options'),
        ];
    }

    public function canOrchestrate(): string
    {
        return 'detail-module';
    }
}
