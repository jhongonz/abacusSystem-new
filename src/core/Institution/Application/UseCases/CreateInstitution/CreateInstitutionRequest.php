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
    private Institution $institution;

    public function __construct(
        Institution $institution
    ) {
        $this->institution = $institution;
    }

    public function institution(): Institution
    {
        return $this->institution;
    }
}
