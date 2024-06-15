<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 17:02:27
 */

namespace App\Http\Orchestrators\Orchestrator\Module;

use App\Http\Orchestrators\Orchestrator\Orchestrator;
use Core\Profile\Domain\Contracts\ModuleManagementContract;

/**
 * @codeCoverageIgnore
 */
abstract class ModuleOrchestrator implements Orchestrator
{
    protected ModuleManagementContract $moduleManagement;

    public function __construct(
        ModuleManagementContract $moduleManagement
    ) {
        $this->moduleManagement = $moduleManagement;
    }
}
