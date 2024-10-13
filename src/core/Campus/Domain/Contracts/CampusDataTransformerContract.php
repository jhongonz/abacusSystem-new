<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 13:31:38
 */

namespace Core\Campus\Domain\Contracts;

use Core\Campus\Domain\Campus;

interface CampusDataTransformerContract
{
    public function write(Campus $campus): CampusDataTransformerContract;

    public function read(): array;

    public function readToShare(): array;
}
