<?php

namespace Core\Employee\Application\UseCases\SearchEmployee;

use Core\Employee\Application\UseCases\RequestService;
use Core\Employee\Domain\ValueObjects\EmployeeId;

class SearchEmployeeByIdRequest implements RequestService
{
    private EmployeeId $id;

    public function __construct(EmployeeId $id)
    {
        $this->id = $id;
    }

    public function employeeId(): EmployeeId
    {
        return $this->id;
    }
}
