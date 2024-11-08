<?php

namespace Core\Employee\Application\UseCases\SearchEmployee;

use Core\Employee\Application\UseCases\RequestService;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;

class SearchEmployeeByIdentificationRequest implements RequestService
{
    public function __construct(
        private readonly EmployeeIdentification $identification
    ) {
    }

    public function employeeIdentification(): EmployeeIdentification
    {
        return $this->identification;
    }
}
