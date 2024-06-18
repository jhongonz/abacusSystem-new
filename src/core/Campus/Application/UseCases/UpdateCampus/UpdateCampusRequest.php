<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 17:19:57
 */

namespace Core\Campus\Application\UseCases\UpdateCampus;

use Core\Campus\Application\UseCases\RequestService;
use Core\Campus\Domain\ValueObjects\CampusId;

class UpdateCampusRequest implements RequestService
{
    private CampusId $id;
    private array $data;

    public function __construct(
        CampusId $id,
        array $data
    ) {
        $this->id = $id;
        $this->data = $data;
    }

    public function id(): CampusId
    {
        return $this->id;
    }

    public function data(): array
    {
        return $this->data;
    }
}
