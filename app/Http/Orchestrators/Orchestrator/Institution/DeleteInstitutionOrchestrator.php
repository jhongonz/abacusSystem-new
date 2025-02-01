<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 07:45:17
 */

namespace App\Http\Orchestrators\Orchestrator\Institution;

use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Illuminate\Http\Request;

class DeleteInstitutionOrchestrator extends InstitutionOrchestrator
{
    public function __construct(
        InstitutionManagementContract $institutionManagement,
    ) {
        parent::__construct($institutionManagement);
    }

    /**
     * @return array<null>
     */
    public function make(Request $request): array
    {
        $this->institutionManagement->deleteInstitution($request->integer('institutionId'));

        return [];
    }

    public function canOrchestrate(): string
    {
        return 'delete-institution';
    }
}
