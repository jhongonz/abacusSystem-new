<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-19 14:50:09
 */

namespace App\Http\Orchestrators\Orchestrator\Campus;

use Core\Campus\Domain\Contracts\CampusManagementContract;
use Illuminate\Http\Request;

class DeleteCampusOrchestrator extends CampusOrchestrator
{
    public function __construct(CampusManagementContract $campusManagement)
    {
        parent::__construct($campusManagement);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function make(Request $request): bool
    {
        $this->campusManagement->deleteCampus($request->input('campusId'));
        return true;
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'delete-campus';
    }
}
