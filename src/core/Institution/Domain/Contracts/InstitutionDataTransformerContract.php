<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-20 07:45:36
 */

namespace Core\Institution\Domain\Contracts;

use Core\Institution\Domain\Institution;

interface InstitutionDataTransformerContract
{
    public function write(Institution $institution): InstitutionDataTransformerContract;

    /**
     * @return array<int|string, array<string, mixed>>
     */
    public function read(): array;

    /**
     * @return array<string, mixed>
     */
    public function readToShare(): array;
}
