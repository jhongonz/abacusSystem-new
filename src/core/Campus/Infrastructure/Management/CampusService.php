<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 16:44:49
 */

namespace Core\Campus\Infrastructure\Management;

use Core\Campus\Application\UseCases\CreateCampus\CreateCampus;
use Core\Campus\Application\UseCases\CreateCampus\CreateCampusRequest;
use Core\Campus\Application\UseCases\DeleteCampus\DeleteCampus;
use Core\Campus\Application\UseCases\DeleteCampus\DeleteCampusRequest;
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
    private CreateCampus $createCampus;
    private DeleteCampus $deleteCampus;

    public function __construct(
        CampusFactoryContract $campusFactory,
        SearchCampusById $searchCampusById,
        SearchCampusCollection $searchCampusCollection,
        UpdateCampus $updateCampus,
        CreateCampus $createCampus,
        DeleteCampus $deleteCampus,
    ) {
        $this->campusFactory = $campusFactory;
        $this->searchCampusById = $searchCampusById;
        $this->searchCampusCollection = $searchCampusCollection;
        $this->updateCampus = $updateCampus;
        $this->createCampus = $createCampus;
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

    /**
     * @throws Exception
     */
    public function createCampus(array $data): Campus
    {
        $request = new CreateCampusRequest(
            $this->campusFactory->buildCampusFromArray($data)
        );

        return $this->createCampus->execute($request);
    }

    /**
     * @throws Exception
     */
    public function deleteCampus(int $id): void
    {
        $request = new DeleteCampusRequest(
            $this->campusFactory->buildCampusId($id)
        );

        $this->deleteCampus->execute($request);
    }
}
