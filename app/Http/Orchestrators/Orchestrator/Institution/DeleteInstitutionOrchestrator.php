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
        InstitutionManagementContract $institutionManagement
    ) {
        parent::__construct($institutionManagement);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function make(Request $request): bool
    {
        $this->institutionManagement->deleteInstitution($request->input('institutionId'));

        return true;
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'delete-institution';
    }
}
