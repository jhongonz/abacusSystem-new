<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 07:02:01
 */

namespace App\Http\Orchestrators\Orchestrator\Institution;

use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Exceptions\InstitutionNotFoundException;
use Illuminate\Http\Request;

class ChangeStateInstitutionOrchestrator extends InstitutionOrchestrator
{
    public function __construct(
        InstitutionManagementContract $institutionManagement,
    ) {
        parent::__construct($institutionManagement);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws InstitutionNotFoundException
     */
    public function make(Request $request): array
    {
        $institutionId = $request->integer('institutionId');
        $institution = $this->institutionManagement->searchInstitutionById($institutionId);

        if (is_null($institution)) {
            throw new InstitutionNotFoundException(sprintf('Institution with id %d not found', $institutionId));
        }

        $institutionState = $institution->state();
        if ($institutionState->isNew() || $institutionState->isInactivated()) {
            $institutionState->activate();
        } elseif ($institutionState->isActivated()) {
            $institutionState->inactive();
        }

        $dataUpdate['state'] = $institutionState->value();
        $this->institutionManagement->updateInstitution($institutionId, $dataUpdate);

        return ['institution' => $institution];
    }

    public function canOrchestrate(): string
    {
        return 'change-state-institution';
    }
}
