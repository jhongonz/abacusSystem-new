<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 16:40:39
 */

namespace Core\Campus\Domain\Contracts;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;

interface CampusManagementContract
{
    public function searchCampusById(int $id): ?Campus;

    public function searchCampusCollection(int $institutionId, array $filters = []): ?CampusCollection;

    public function updateCampus(int $id, array $data): Campus;

    public function createCampus(array $data): Campus;

    public function deleteCampus(int $id): void;
}
