<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-19 11:00:29
 */

namespace App\Http\Orchestrators\Orchestrator\Campus;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusManagementContract;
use Illuminate\Http\Request;

class UpdateCampusOrchestrator extends CampusOrchestrator
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
        $dataUpdate = json_decode($request->input('dataUpdate'), true);

        return $this->campusManagement->updateCampus($request->input('campusId'), $dataUpdate);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'update-campus';
    }
}
