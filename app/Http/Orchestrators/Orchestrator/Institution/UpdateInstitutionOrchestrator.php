<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 07:34:51
 */

namespace App\Http\Orchestrators\Orchestrator\Institution;

use App\Traits\UtilsDateTimeTrait;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Illuminate\Http\Request;

class UpdateInstitutionOrchestrator extends InstitutionOrchestrator
{
    use UtilsDateTimeTrait;

    public function __construct(
        InstitutionManagementContract $institutionManagement,
    ) {
        parent::__construct($institutionManagement);
    }

    /**
     * @param Request $request
     * @return Institution
     */
    public function make(Request $request): Institution
    {
        $dataUpdate = json_decode($request->input('dataUpdate'), true);
        $dataUpdate['updatedAt'] = $this->getCurrentTime();

        return $this->institutionManagement->updateInstitution($request->input('institutionId'), $dataUpdate);
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'update-institution';
    }
}
