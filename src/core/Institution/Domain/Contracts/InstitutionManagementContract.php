<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-20 21:39:55
 */

namespace Core\Institution\Domain\Contracts;

use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;

interface InstitutionManagementContract
{
    public function searchInstitutionById(?int $id): ?Institution;

    /**
     * @param array<string, mixed> $filters
     */
    public function searchInstitutions(array $filters = []): Institutions;

    /**
     * @param array<string, mixed> $data
     */
    public function updateInstitution(int $id, array $data): Institution;

    /**
     * @param array<string, mixed> $data
     */
    public function createInstitution(array $data): Institution;

    public function deleteInstitution(int $id): void;
}
