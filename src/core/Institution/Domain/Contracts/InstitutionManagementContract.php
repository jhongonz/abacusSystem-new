<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-20 21:39:55
 */

namespace Core\Institution\Domain\Contracts;

use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;
use Core\Institution\Domain\ValueObjects\InstitutionId;

interface InstitutionManagementContract
{
    public function searchInstitutionById(InstitutionId $id): ?Institution;

    public function searchInstitutions(array $filters = []): Institutions;

    public function updateInstitution(InstitutionId $id, array $data): void;

    public function createInstitution(Institution $institution): void;

    public function deleteInstitution(InstitutionId $id): void;
}
