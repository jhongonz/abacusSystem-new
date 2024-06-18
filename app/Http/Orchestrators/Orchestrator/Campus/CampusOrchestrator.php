<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-18 09:51:00
 */

namespace App\Http\Orchestrators\Orchestrator\Campus;

use App\Http\Orchestrators\Orchestrator\Orchestrator;
use Core\Campus\Domain\Contracts\CampusManagementContract;

/**
 * @codeCoverageIgnore
 */
abstract class CampusOrchestrator implements Orchestrator
{
    protected CampusManagementContract $campusManagement;

    public function __construct(
        CampusManagementContract $campusManagement
    ) {
        $this->campusManagement = $campusManagement;
    }
}
