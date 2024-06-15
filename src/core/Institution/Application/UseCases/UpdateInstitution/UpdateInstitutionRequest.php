<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-21 21:56:07
 */

namespace Core\Institution\Application\UseCases\UpdateInstitution;

use Core\Institution\Application\UseCases\RequestService;
use Core\Institution\Domain\ValueObjects\InstitutionId;

class UpdateInstitutionRequest implements RequestService
{
    private InstitutionId $id;
    private array $data;

    public function __construct(
        InstitutionId $id,
        array $data
    ) {
        $this->id = $id;
        $this->data = $data;
    }

    public function id(): InstitutionId
    {
        return $this->id;
    }

    public function data(): array
    {
        return $this->data;
    }
}
