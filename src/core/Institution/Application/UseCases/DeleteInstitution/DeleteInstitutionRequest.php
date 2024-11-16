<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-21 22:21:44
 */

namespace Core\Institution\Application\UseCases\DeleteInstitution;

use Core\Institution\Application\UseCases\RequestService;
use Core\Institution\Domain\ValueObjects\InstitutionId;

class DeleteInstitutionRequest implements RequestService
{
    public function __construct(
        private readonly InstitutionId $id,
    ) {
    }

    public function id(): InstitutionId
    {
        return $this->id;
    }
}
