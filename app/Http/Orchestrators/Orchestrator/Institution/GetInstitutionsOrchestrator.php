<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 07:11:30
 */

namespace App\Http\Orchestrators\Orchestrator\Institution;

use Core\Institution\Domain\Contracts\InstitutionDataTransformerContract;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Illuminate\Http\Request;

class GetInstitutionsOrchestrator extends InstitutionOrchestrator
{
    public function __construct(
        InstitutionManagementContract $institutionManagement,
        private readonly InstitutionDataTransformerContract $institutionDataTransformer
    ) {
        parent::__construct($institutionManagement);
    }

    /**
     * @param Request $request
     * @return array<int|string, mixed>
     */
    public function make(Request $request): array
    {
        $institutions = $this->institutionManagement->searchInstitutions(
            (array) $request->input('filters')
        );

        $dataInstitutions = [];
        if ($institutions->count()) {
            /** @var Institution $item */
            foreach ($institutions as $item) {
                $dataInstitutions[] = $this->institutionDataTransformer->write($item)->readToShare();
            }
        }

        return $dataInstitutions;
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'retrieve-institutions';
    }
}
