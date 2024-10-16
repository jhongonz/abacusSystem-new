<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 17:50:53
 */

namespace Core\Campus\Application\UseCases\CreateCampus;

use Core\Campus\Application\UseCases\RequestService;
use Core\Campus\Domain\Campus;

class CreateCampusRequest implements RequestService
{
    public function __construct(
        private readonly Campus $campus
    ) {
    }

    public function campus(): Campus
    {
        return $this->campus;
    }
}
