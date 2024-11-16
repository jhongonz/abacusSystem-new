<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 06:58:36
 */

namespace App\Http\Orchestrators\Orchestrator\Institution;

use App\Http\Orchestrators\Orchestrator\Orchestrator;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;

abstract class InstitutionOrchestrator implements Orchestrator
{
    public function __construct(
        protected readonly InstitutionManagementContract $institutionManagement,
    ) {
    }
}
