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
        private readonly InstitutionDataTransformerContract $institutionDataTransformer,
    ) {
        parent::__construct($institutionManagement);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function make(Request $request): array
    {
        /** @var array<string ,mixed> $filters */
        $filters = $request->input('filters');

        $institutions = $this->institutionManagement->searchInstitutions($filters);

        $dataInstitutions = [];
        if ($institutions->count()) {
            /** @var Institution $item */
            foreach ($institutions as $item) {
                $dataInstitutions[] = $this->institutionDataTransformer->write($item)->readToShare();
            }
        }

        return $dataInstitutions;
    }

    public function canOrchestrate(): string
    {
        return 'retrieve-institutions';
    }
}
