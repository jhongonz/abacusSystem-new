<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 16:44:49
 */

namespace Core\Campus\Infrastructure\Management;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;
use Core\Campus\Domain\Contracts\CampusFactoryContract;
use Core\Campus\Domain\Contracts\CampusManagementContract;

class CampusService implements CampusManagementContract
{
    private CampusFactoryContract $campusFactory;

    public function __construct(
        CampusFactoryContract $campusFactory
    ) {
        $this->campusFactory = $campusFactory;
    }

    public function searchCampusById(int $id): ?Campus
    {
        // TODO: Implement searchCampusById() method.
    }

    public function searchCampusCollection(int $institutionId, array $filters = []): ?CampusCollection
    {
        // TODO: Implement searchCampusCollection() method.
    }

    public function updateCampus(int $id, array $data): Campus
    {
        // TODO: Implement updateCampus() method.
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
