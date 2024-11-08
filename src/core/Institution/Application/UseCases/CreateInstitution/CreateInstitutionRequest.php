<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-21 22:17:17
 */

namespace Core\Institution\Application\UseCases\CreateInstitution;

use Core\Institution\Application\UseCases\RequestService;
use Core\Institution\Domain\Institution;

class CreateInstitutionRequest implements RequestService
{
    public function __construct(
        private readonly Institution $institution
    ) {
    }

    public function institution(): Institution
    {
        return $this->institution;
    }
}
