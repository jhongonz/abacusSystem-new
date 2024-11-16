<?php

namespace Core\Employee\Application\UseCases\SearchEmployee;

use Core\Employee\Application\UseCases\RequestService;
use Core\Employee\Domain\ValueObjects\EmployeeId;

class SearchEmployeeByIdRequest implements RequestService
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
