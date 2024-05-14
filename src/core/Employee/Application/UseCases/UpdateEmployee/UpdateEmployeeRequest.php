<?php

namespace Core\Employee\Application\UseCases\UpdateEmployee;

use Core\Employee\Application\UseCases\RequestService;
use Core\Employee\Domain\ValueObjects\EmployeeId;

class UpdateEmployeeRequest implements RequestService
{
    private EmployeeId $id;

    private array $data;

    public function __construct(
        EmployeeId $id,
        array $data,
    ) {
        $this->id = $id;
        $this->data = $data;
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
