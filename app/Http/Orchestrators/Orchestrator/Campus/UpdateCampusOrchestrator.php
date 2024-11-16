<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-19 11:00:29
 */

namespace App\Http\Orchestrators\Orchestrator\Campus;

use Core\Campus\Domain\Contracts\CampusManagementContract;
use Illuminate\Http\Request;

class UpdateCampusOrchestrator extends CampusOrchestrator
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
        $dataUpdate = [
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'observations' => $request->input('observations'),
        ];

        $campus = $this->campusManagement->updateCampus($request->integer('campusId'), $dataUpdate);

        return ['campus' => $campus];
    }

    public function canOrchestrate(): string
    {
        return 'update-campus';
    }
}
