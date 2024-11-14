<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 18:13:48
 */

namespace App\Http\Orchestrators\Orchestrator\Module;

use Core\Profile\Domain\Contracts\ModuleDataTransformerContract;
use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Module;
use Illuminate\Http\Request;

class GetModulesOrchestrator extends ModuleOrchestrator
{
    public function __construct(
        ModuleManagementContract $moduleManagement,
        private readonly ModuleDataTransformerContract $moduleDataTransformer,
    ) {
        parent::__construct($moduleManagement);
    }

    /**
     * @param Request $request
     * @return array<int|string, mixed>
     */
    public function make(Request $request): array
    {
        $modules = $this->moduleManagement->searchModules(
            (array) $request->input('filters')
        );

        $dataModules = [];
        if ($modules->count()) {
            /** @var Module $item */
            foreach ($modules as $item) {
                $dataModules[] = $this->moduleDataTransformer->write($item)->readToShare();
            }
        }

        return $dataModules;
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'retrieve-modules';
    }
}
