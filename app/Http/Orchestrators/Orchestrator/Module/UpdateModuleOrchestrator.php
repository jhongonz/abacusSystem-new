<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 18:04:20
 */

namespace App\Http\Orchestrators\Orchestrator\Module;

use App\Traits\UtilsDateTimeTrait;
use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Module;
use Illuminate\Http\Request;

class UpdateModuleOrchestrator extends ModuleOrchestrator
{
    use UtilsDateTimeTrait;

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
        $dataUpdate = json_decode($request->input('dataUpdate'), true);
        $dataUpdate['updateAt'] = $this->getCurrentTime();

        $moduleId = $request->input('moduleId');
        return $this->moduleManagement->updateModule($moduleId, $dataUpdate);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'update-module';
    }
}
