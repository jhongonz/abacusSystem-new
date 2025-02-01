<?php

namespace Core\Employee\Domain\Contracts;

use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Domain\ValueObjects\EmployeeAddress;
use Core\Employee\Domain\ValueObjects\EmployeeBirthdate;
use Core\Employee\Domain\ValueObjects\EmployeeCreatedAt;
use Core\Employee\Domain\ValueObjects\EmployeeEmail;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use Core\Employee\Domain\ValueObjects\EmployeeIdentificationType;
use Core\Employee\Domain\ValueObjects\EmployeeImage;
use Core\Employee\Domain\ValueObjects\EmployeeInstitutionId;
use Core\Employee\Domain\ValueObjects\EmployeeLastname;
use Core\Employee\Domain\ValueObjects\EmployeeName;
use Core\Employee\Domain\ValueObjects\EmployeeObservations;
use Core\Employee\Domain\ValueObjects\EmployeePhone;
use Core\Employee\Domain\ValueObjects\EmployeeSearch;
use Core\Employee\Domain\ValueObjects\EmployeeState;
use Core\Employee\Domain\ValueObjects\EmployeeUpdatedAt;
use Core\Employee\Domain\ValueObjects\EmployeeUserId;
use Core\SharedContext\Model\ValueObjectStatus;

interface EmployeeFactoryContract
{
    /**
     * @param array<string, mixed> $data
     */
    public function buildEmployeeFromArray(array $data): Employee;

    public function buildEmployee(
        EmployeeId $id,
        EmployeeIdentification $identification,
        EmployeeName $name,
        EmployeeLastname $lastname = new EmployeeLastname(),
        EmployeeState $state = new EmployeeState(),
        EmployeeCreatedAt $createdAt = new EmployeeCreatedAt(),
    ): Employee;

    public function buildEmployeeId(?int $id = null): EmployeeId;

    public function buildEmployeeUserId(?int $id = null): EmployeeUserId;

    public function buildEmployeeIdentification(string $identification): EmployeeIdentification;

    public function buildEmployeeName(string $name): EmployeeName;

    public function buildEmployeeLastname(string $lastname): EmployeeLastname;

    public function buildEmployeePhone(?string $phone = null): EmployeePhone;

    public function buildEmployeeEmail(?string $email = null): EmployeeEmail;

    public function buildEmployeeAddress(?string $address): EmployeeAddress;

    public function buildEmployeeState(int $state = ValueObjectStatus::STATE_NEW): EmployeeState;

    public function buildEmployeeSearch(?string $search = null): EmployeeSearch;

    public function buildEmployeeCreatedAt(\DateTime $datetime = new \DateTime('now')): EmployeeCreatedAt;

    public function buildEmployeeUpdatedAt(?\DateTime $datetime = null): EmployeeUpdatedAt;

    public function buildEmployeeBirthdate(?\DateTime $date = null): EmployeeBirthdate;

    public function buildEmployeeObservations(?string $observations = null): EmployeeObservations;

    public function buildEmployeeIdentificationType(?string $type = null): EmployeeIdentificationType;

    public function buildEmployeeImage(?string $image = null): EmployeeImage;

    public function buildEmployeeInstitutionId(?int $institutionId = null): EmployeeInstitutionId;

    public function buildEmployees(Employee ...$employees): Employees;
}
