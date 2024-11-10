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
    /**
     * @param InstitutionId $id
     * @param array<string, mixed> $data
     */
    public function __construct(
        private readonly InstitutionId $id,
        private readonly array $data
    ) {
    }

    public function id(): InstitutionId
    {
        return $this->id;
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return $this->data;
    }
}
