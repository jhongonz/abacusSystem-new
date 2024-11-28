<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-19 08:36:36
 */

namespace App\Http\Orchestrators\Orchestrator\Campus;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusManagementContract;
use Core\SharedContext\Model\ValueObjectStatus;
use Illuminate\Http\Request;

class CreateCampusOrchestrator extends CampusOrchestrator
{
    public function __construct(CampusManagementContract $campusManagement)
    {
        parent::__construct($campusManagement);
    }

    /**
     * @return array<string, mixed>
     */
    public function make(Request $request): array
    {
        $dataCampus = [
            'id' => $request->integer('campusId'),
            'institutionId' => $request->integer('institutionId'),
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'observations' => $request->input('observations'),
            'state' => ValueObjectStatus::STATE_NEW,
        ];

        $campus = $this->campusManagement->createCampus([Campus::TYPE => $dataCampus]);

        return ['campus' => $campus];
    }

    public function canOrchestrate(): string
    {
        return 'create-campus';
    }
}
