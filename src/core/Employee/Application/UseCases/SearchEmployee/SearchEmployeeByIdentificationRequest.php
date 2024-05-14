<?php

namespace Core\Employee\Application\UseCases\SearchEmployee;

use Core\Employee\Application\UseCases\RequestService;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;

class SearchEmployeeByIdentificationRequest implements RequestService
{
    private EmployeeIdentification $identification;

    public function __construct(EmployeeIdentification $identification)
    {
        $this->identification = $identification;
    }

    public function employeeIdentification(): EmployeeIdentification
    {
        return $this->identification;
    }
}
