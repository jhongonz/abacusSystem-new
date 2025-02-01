<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 13:44:27
 */

namespace Core\Campus\Domain\Contracts;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;
use Core\Campus\Domain\ValueObjects\CampusId;
use Core\Campus\Domain\ValueObjects\CampusInstitutionId;

interface CampusRepositoryContract
{
    public function find(CampusId $id): ?Campus;

    /**
     * @param array<string, mixed> $filters
     */
    public function getAll(CampusInstitutionId $id, array $filters = []): ?CampusCollection;

    public function delete(CampusId $id): void;

    public function persistCampus(Campus $campus): Campus;
}
