<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 17:53:22
 */

namespace App\Http\Orchestrators\Orchestrator\Module;

use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Module;
use Illuminate\Http\Request;

class CreateModuleOrchestrator extends ModuleOrchestrator
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
        $dataModule = [
            'id' => null,
            'key' => $request->input('key'),
            'name' => $request->input('name'),
            'route' => $request->input('route'),
            'icon' => $request->input('icon'),
        ];

        return $this->moduleManagement->createModule([Module::TYPE => $dataModule]);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'create-module';
    }
}
