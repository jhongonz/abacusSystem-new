<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-21 12:45:09
 */

namespace Core\Institution\Application\UseCases\SearchInstitution;

use Core\Institution\Application\UseCases\RequestService;
use Core\Institution\Domain\ValueObjects\InstitutionId;

class SearchInstitutionByIdRequest implements RequestService
{
    public function __construct(
        private readonly InstitutionId $id,
    ) {
    }

    public function institutionId(): InstitutionId
    {
        return $this->id;
    }
}
