<?php

namespace Core\Employee\Domain\Contracts;

use Core\Employee\Domain\Employee;
use Core\Employee\Domain\ValueObjects\EmployeeAddress;
use Core\Employee\Domain\ValueObjects\EmployeeCreatedAt;
use Core\Employee\Domain\ValueObjects\EmployeeEmail;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use Core\Employee\Domain\ValueObjects\EmployeeLastname;
use Core\Employee\Domain\ValueObjects\EmployeeName;
use Core\Employee\Domain\ValueObjects\EmployeePhone;
use Core\Employee\Domain\ValueObjects\EmployeeState;
use Core\Employee\Domain\ValueObjects\EmployeeUpdateAt;
use Core\Employee\Domain\ValueObjects\EmployeeUserId;
use DateTime;

interface EmployeeFactoryContract
{
    public function buildEmployeeFromArray(array $data): Employee;

    public function buildEmployee(
        EmployeeId $id,
        EmployeeIdentification $identification,
        EmployeeName $name,
        null|EmployeeLastname $lastname,
        null|EmployeeState $state,
        null|EmployeeCreatedAt $createdAt
    ): Employee;

    public function buildEmployeeId(int $id): EmployeeId;

    public function buildEmployeeUserId(null|int $id = null): EmployeeUserId;

    public function buildEmployeeIdentification(string $identification): EmployeeIdentification;

    public function buildEmployeeName(string $name): EmployeeName;

    public function buildEmployeeLastname(string $lastname): EmployeeLastname;

    public function buildEmployeePhone(string $phone): EmployeePhone;

    public function buildEmployeeEmail(string $email): EmployeeEmail;

    public function buildEmployeeAddress(string $address): EmployeeAddress;

    public function buildEmployeeState(null|int $state = null): EmployeeState;

    public function buildEmployeeCreatedAt(null|DateTime $datetime = null): EmployeeCreatedAt;

    public function buildEmployeeUpdatedAt(null|DateTime $datetime = null): EmployeeUpdateAt;
}
