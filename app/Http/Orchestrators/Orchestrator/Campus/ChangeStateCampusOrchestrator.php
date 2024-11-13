<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-19 11:08:55
 */

namespace App\Http\Orchestrators\Orchestrator\Campus;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusManagementContract;
use Core\Campus\Exceptions\CampusNotFoundException;
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
     * @throws CampusNotFoundException
     */
    public function make(Request $request): Campus
    {
        $campusId = $request->integer('campusId');
        $campus = $this->campusManagement->searchCampusById($campusId);

        if (is_null($campus)) {
            throw new CampusNotFoundException(sprintf('Campus not found with id %s', $campusId));
        }

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
