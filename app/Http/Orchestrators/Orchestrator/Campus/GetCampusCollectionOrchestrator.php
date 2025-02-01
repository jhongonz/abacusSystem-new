<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-18 09:52:41
 */

namespace App\Http\Orchestrators\Orchestrator\Campus;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusDataTransformerContract;
use Core\Campus\Domain\Contracts\CampusManagementContract;
use Illuminate\Http\Request;

class GetCampusCollectionOrchestrator extends CampusOrchestrator
{
    public function __construct(
        CampusManagementContract $campusManagement,
        private readonly CampusDataTransformerContract $campusDataTransformer,
    ) {
        parent::__construct($campusManagement);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function make(Request $request): array
    {
        /** @var array<string, mixed> $filters */
        $filters = $request->input('filters');

        $campusCollection = $this->campusManagement->searchCampusCollection(
            $request->integer('institutionId'),
            $filters
        );

        $dataCampus = [];
        if (!is_null($campusCollection) && $campusCollection->count()) {
            /** @var Campus $item */
            foreach ($campusCollection as $item) {
                $dataCampus[] = $this->campusDataTransformer->write($item)->readToShare();
            }
        }

        return $dataCampus;
    }

    public function canOrchestrate(): string
    {
        return 'retrieve-campus-collection';
    }
}
