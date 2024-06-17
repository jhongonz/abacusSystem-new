<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 16:44:49
 */

namespace Core\Campus\Infrastructure\Management;

use Core\Campus\Application\UseCases\SearchCampus\SearchCampusById;
use Core\Campus\Application\UseCases\SearchCampus\SearchCampusByIdRequest;
use Core\Campus\Application\UseCases\SearchCampus\SearchCampusCollection;
use Core\Campus\Application\UseCases\SearchCampus\SearchCampusCollectionRequest;
use Core\Campus\Application\UseCases\UpdateCampus\UpdateCampus;
use Core\Campus\Application\UseCases\UpdateCampus\UpdateCampusRequest;
use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;
use Core\Campus\Domain\Contracts\CampusFactoryContract;
use Core\Campus\Domain\Contracts\CampusManagementContract;
use Exception;

class CampusService implements CampusManagementContract
{
    private CampusFactoryContract $campusFactory;
    private SearchCampusById $searchCampusById;
    private SearchCampusCollection $searchCampusCollection;
    private UpdateCampus $updateCampus;

    public function __construct(
        CampusFactoryContract $campusFactory,
        SearchCampusById $searchCampusById,
        SearchCampusCollection $searchCampusCollection,
        UpdateCampus $updateCampus
    ) {
        $this->campusFactory = $campusFactory;
        $this->searchCampusById = $searchCampusById;
        $this->searchCampusCollection = $searchCampusCollection;
        $this->updateCampus = $updateCampus;
    }

    /**
     * @throws Exception
     */
    public function searchCampusById(int $id): ?Campus
    {
        $request = new SearchCampusByIdRequest(
            $this->campusFactory->buildCampusId($id)
        );

        return $this->searchCampusById->execute($request);
    }

    /**
     * @throws Exception
     */
    public function searchCampusCollection(int $institutionId, array $filters = []): ?CampusCollection
    {
        $request = new SearchCampusCollectionRequest(
            $this->campusFactory->buildCampusInstitutionId($institutionId),
            $filters
        );
        $campusCollection = $this->searchCampusCollection->execute($request);

        foreach ($campusCollection->aggregator() as $item) {
            $campus = $this->searchCampusById($item);
            $campusCollection->addItem($campus);
        }

        return $campusCollection;
    }

    /**
     * @throws Exception
     */
    public function updateCampus(int $id, array $data): Campus
    {
        $request = new UpdateCampusRequest(
            $this->campusFactory->buildCampusId($id),
            $data
        );

        return $this->updateCampus->execute($request);
    }

    public function createCampus(array $data): Campus
    {
        // TODO: Implement createCampus() method.
    }

    public function deleteCampus(int $id): void
    {
        // TODO: Implement deleteCampus() method.
    }
}
