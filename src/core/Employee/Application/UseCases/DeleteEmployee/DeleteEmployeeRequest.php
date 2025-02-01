<?php

namespace Core\Employee\Application\UseCases\DeleteEmployee;

use Core\Employee\Application\UseCases\RequestService;
use Core\Employee\Domain\ValueObjects\EmployeeId;

class DeleteEmployeeRequest implements RequestService
{
    public function __construct(
        private readonly EmployeeId $id,
    ) {
    }

    public function employeeId(): EmployeeId
    {
        return $this->id;
    }
}
