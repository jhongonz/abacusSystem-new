<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-18 09:52:41
 */

namespace App\Http\Orchestrators\Orchestrator\Campus;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusDataTransformerContract;
use Core\Campus\Domain\Contracts\CampusManagementContract;
use Core\Campus\Exceptions\CampusCollectionNotFoundException;
use Illuminate\Http\Request;

/**
 * @template TKey of array-key
 * @template-covariant TValue
 */
class GetCampusCollectionOrchestrator extends CampusOrchestrator
{
    public function __construct(
        CampusManagementContract $campusManagement,
        private readonly CampusDataTransformerContract $campusDataTransformer,
    ) {
        parent::__construct($campusManagement);
    }

    /**
     * @param Request $request
     * @return array<int|string, mixed>
     * @throws CampusCollectionNotFoundException
     */
    public function make(Request $request): array
    {
        $campusCollection = $this->campusManagement->searchCampusCollection(
            $request->integer('institutionId'),
            (array) $request->input('filters')
        );

        if (is_null($campusCollection)) {
            throw new CampusCollectionNotFoundException('Campus collection not found');
        }

        $dataCampus = [];
        if ($campusCollection->count()) {
            /** @var Campus $item */
            foreach ($campusCollection as $item) {
                $dataCampus[] = $this->campusDataTransformer->write($item)->readToShare();
            }
        }

        return $dataCampus;
    }

    /**
     * @return string
     */
    public function canOrchestrate(): string
    {
        return 'retrieve-campus-collection';
    }
}
