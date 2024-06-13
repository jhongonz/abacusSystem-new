<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 17:42:08
 */

namespace App\Http\Orchestrators\Orchestrator\Module;

use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Module;
use Illuminate\Config\Repository as Config;
use Illuminate\Http\Request;

class DetailModuleOrchestrator extends ModuleOrchestrator
{
    private Config $config;

    public function __construct(
        ModuleManagementContract $moduleManagement,
        Config $config
    ) {
        parent::__construct($moduleManagement);
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function make(Request $request): array
    {
        $moduleId = $request->input('moduleId');
        if (isset($moduleId)) {

            /** @var Module $module */
            $module = $this->moduleManagement->searchModuleById($moduleId);
        }

        return [
            'moduleId' => $moduleId,
            'module' => $module ?? null,
            'menuKeys' => $this->config->get('menu.options')
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
