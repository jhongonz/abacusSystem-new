<?php

namespace Core\Employee\Application\UseCases\UpdateEmployee;

use Core\Employee\Application\UseCases\RequestService;
use Core\Employee\Domain\ValueObjects\EmployeeId;

class UpdateEmployeeRequest implements RequestService
{
    public function __construct(
        private readonly EmployeeId $id,
        private readonly array $data,
    ) {
    }

    public function employeeId(): EmployeeId
    {
        return $this->id;
    }

    public function data(): array
    {
        return $this->data;
    }
}
