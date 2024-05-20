<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-20 21:57:42
 */

namespace Core\Institution\Infrastructure\Persistence\Repositories;

use Core\Institution\Domain\Contracts\InstitutionRepositoryContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Core\Institution\Domain\ValueObjects\InstitutionId;

class RedisInstitutionRepository implements InstitutionRepositoryContract
{
    public function find(InstitutionId $id): ?Institution
    {
        // TODO: Implement find() method.
    }

    public function getAll(array $filters = []): Institutions
    {
        // TODO: Implement getAll() method.
    }

    public function delete(InstitutionId $id): void
    {
        // TODO: Implement delete() method.
    }

    public function persistInstitution(Institution $institution): Institution
    {
        // TODO: Implement persistInstitution() method.
    }
}
