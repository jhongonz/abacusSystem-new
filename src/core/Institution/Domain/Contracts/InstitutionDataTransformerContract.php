<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-20 07:45:36
 */

namespace Core\Institution\Domain\Contracts;

use Core\Institution\Application\DataTransformer\InstitutionDataTransformer;
use Core\Institution\Domain\Institution;

interface InstitutionDataTransformerContract
{
    public function write(Institution $institution): InstitutionDataTransformer;

    public function read(): array;

    public function readToShare(): array;
}
