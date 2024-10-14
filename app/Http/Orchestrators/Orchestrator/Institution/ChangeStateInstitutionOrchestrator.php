<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 07:02:01
 */

namespace App\Http\Orchestrators\Orchestrator\Institution;

use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Illuminate\Http\Request;

class ChangeStateInstitutionOrchestrator extends InstitutionOrchestrator
{
    public function __construct(
        InstitutionManagementContract $institutionManagement
    ) {
        parent::__construct($institutionManagement);
    }

    /**
     * @param Request $request
     * @return Institution
     */
    public function make(Request $request): Institution
    {
        $institutionId = $request->input('institutionId');

        /** @var Institution $institution */
        $institution = $this->institutionManagement->searchInstitutionById($institutionId);

        $institutionState = $institution->state();
        if ($institutionState->isNew() || $institutionState->isInactivated()) {
            $institutionState->activate();
        } elseif ($institutionState->isActivated()) {
            $institutionState->inactive();
        }

        $dataUpdate['state'] = $institutionState->value();

        return $this->institutionManagement->updateInstitution($institutionId, $dataUpdate);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'change-state-institution';
    }
}
