<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-18 23:05:46
 */

namespace App\Http\Orchestrators\Orchestrator\Campus;

use Core\Campus\Domain\Contracts\CampusManagementContract;
use Illuminate\Http\Request;

class DetailCampusOrchestrator extends CampusOrchestrator
{
    public function __construct(
        CampusManagementContract $campusManagement,
    ) {
        parent::__construct($campusManagement);
    }

    /**
     * @return array<string, mixed>
     */
    public function make(Request $request): array
    {
        $campusId = $request->integer('campusId') ?: null;

        if (!is_null($campusId)) {
            $campus = $this->campusManagement->searchCampusById($campusId);
        }

        return [
            'campusId' => $campusId,
            'campus' => $campus ?? null,
        ];
    }

    public function canOrchestrate(): string
    {
        return 'detail-campus';
    }
}
