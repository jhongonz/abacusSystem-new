<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 06:58:36
 */

namespace App\Http\Orchestrators\Orchestrator\Institution;

use App\Http\Orchestrators\Orchestrator\Orchestrator;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;

/**
 * @codeCoverageIgnore
 */
abstract class InstitutionOrchestrator implements Orchestrator
{
    protected InstitutionManagementContract $institutionManagement;

    public function __construct(
        InstitutionManagementContract $institutionManagement
    ) {
        $this->institutionManagement = $institutionManagement;
    }
}
