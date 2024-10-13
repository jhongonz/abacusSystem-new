<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-19 11:08:55
 */

namespace App\Http\Orchestrators\Orchestrator\Campus;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusManagementContract;
use Illuminate\Http\Request;

class ChangeStateCampusOrchestrator extends CampusOrchestrator
{
    public function __construct(CampusManagementContract $campusManagement)
    {
        parent::__construct($campusManagement);
    }

    /**
     * @param Request $request
     * @return Campus
     */
    public function make(Request $request): Campus
    {
        $campusId = $request->input('campusId');
        $campus = $this->campusManagement->searchCampusById($campusId);

        $campusState = $campus->state();
        if ($campusState->isNew() || $campusState->isInactivated()) {
            $campusState->activate();
        } elseif ($campusState->isActivated()) {
            $campusState->inactive();
        }

        $dataUpdate['state'] = $campusState->value();

        return $this->campusManagement->updateCampus($campusId, $dataUpdate);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'change-state-campus';
    }
}
