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
    private InstitutionId $id;

    public function __construct(
        InstitutionId $id
    ) {
        $this->id = $id;
    }

    public function institutionId(): InstitutionId
    {
        return $this->id;
    }
}
