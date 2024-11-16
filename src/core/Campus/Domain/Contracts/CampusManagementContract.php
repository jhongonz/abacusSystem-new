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

    /**
     * @param array<string, mixed> $filters
     */
    public function searchCampusCollection(int $institutionId, array $filters = []): ?CampusCollection;

    /**
     * @param array<string, mixed> $data
     */
    public function updateCampus(int $id, array $data): Campus;

    /**
     * @param array<string, mixed> $data
     */
    public function createCampus(array $data): Campus;

    public function deleteCampus(int $id): void;
}
