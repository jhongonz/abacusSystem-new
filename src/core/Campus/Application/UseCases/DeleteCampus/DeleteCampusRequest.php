<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 17:56:18
 */

namespace Core\Campus\Application\UseCases\DeleteCampus;

use Core\Campus\Application\UseCases\RequestService;
use Core\Campus\Domain\ValueObjects\CampusId;

class DeleteCampusRequest implements RequestService
{
    public function __construct(
        private readonly CampusId $id,
    ) {
    }

    public function id(): CampusId
    {
        return $this->id;
    }
}
