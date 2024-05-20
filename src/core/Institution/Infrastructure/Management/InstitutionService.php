<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-20 21:43:18
 */

namespace Core\Institution\Infrastructure\Management;

use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Core\Institution\Domain\ValueObjects\InstitutionId;

class InstitutionService implements InstitutionManagementContract
{
    public function searchInstitutionById(InstitutionId $id): ?Institution
    {
        // TODO: Implement searchInstitutionById() method.
    }

    public function searchInstitutions(array $filters = []): Institutions
    {
        // TODO: Implement searchInstitutions() method.
    }

    public function updateInstitution(InstitutionId $id, array $data): void
    {
        // TODO: Implement updateInstitution() method.
    }

    public function createInstitution(Institution $institution): void
    {
        // TODO: Implement createInstitution() method.
    }

    public function deleteInstitution(InstitutionId $id): void
    {
        // TODO: Implement deleteInstitution() method.
    }
}
