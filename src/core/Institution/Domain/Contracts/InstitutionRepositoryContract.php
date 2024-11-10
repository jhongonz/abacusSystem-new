<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-20 21:55:11
 */

namespace Core\Institution\Domain\Contracts;

use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Core\Institution\Domain\ValueObjects\InstitutionId;

interface InstitutionRepositoryContract
{
    public function find(InstitutionId $id): ?Institution;

    /**
     * @param array<string, mixed> $filters
     * @return Institutions|null
     */
    public function getAll(array $filters = []): ?Institutions;

    public function delete(InstitutionId $id): void;

    public function persistInstitution(Institution $institution): Institution;
}
