<?php

namespace Core\Employee\Application\Factory;

use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
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
use DateTime;
use Exception;

class EmployeeFactory implements EmployeeFactoryContract
{
    /**
     * @param array<string, mixed> $data
     * @return Employee
     * @throws Exception
     */
    public function buildEmployeeFromArray(array $data): Employee
    {
        /** @var array{
         *     id: int|null,
         *     identification: string,
         *     name: string,
         *     lastname: string,
         *     state: int,
         *     identification_type: string|null,
         *     userId: int|null,
         *     address: string|null,
         *     phone: string|null,
         *     email: string|null,
         *     createdAt: string|null,
         *     updatedAt: string|null,
         *     birthdate: string|null,
         *     observations: string|null,
         *     image: string|null,
         *     institutionId: int|null,
         *     search: string|null
         * } $dataEmployee
         */
        $dataEmployee = $data[Employee::TYPE];

        $employee = $this->buildEmployee(
            $this->buildEmployeeId($dataEmployee['id']),
            $this->buildEmployeeIdentification($dataEmployee['identification']),
            $this->buildEmployeeName($dataEmployee['name']),
            $this->buildEmployeeLastname($dataEmployee['lastname']),
            $this->buildEmployeeState($dataEmployee['state']),
        );

        $employee->setIdentificationType($this->buildEmployeeIdentificationType($dataEmployee['identification_type']));
        $employee->setUserId($this->buildEmployeeUserId($dataEmployee['userId']));
        $employee->setAddress($this->buildEmployeeAddress($dataEmployee['address']));
        $employee->setPhone($this->buildEmployeePhone($dataEmployee['phone']));
        $employee->setEmail($this->buildEmployeeEmail($dataEmployee['email']));

        if (isset($dataEmployee['createdAt'])) {
            $employee->setCreatedAt(
                $this->buildEmployeeCreatedAt($this->getDateTime($dataEmployee['createdAt']))
            );
        }

        if (isset($dataEmployee['updatedAt'])) {
            $employee->setUpdatedAt(
                $this->buildEmployeeUpdatedAt($this->getDateTime($dataEmployee['updatedAt']))
            );
        }

        if (isset($dataEmployee['birthdate'])) {
            $employee->setBirthdate($this->buildEmployeeBirthdate($this->getDateTime($dataEmployee['birthdate'])));
        }

        $employee->setObservations($this->buildEmployeeObservations($dataEmployee['observations']));
        $employee->setImage($this->buildEmployeeImage($dataEmployee['image']));
        $employee->setInstitutionId($this->buildEmployeeInstitutionId($dataEmployee['institutionId']));

        if (isset($dataEmployee['search'])) {
            $employee->setSearch($this->buildEmployeeSearch($dataEmployee['search']));
        }

        return $employee;
    }

    public function buildEmployee(
        EmployeeId $id,
        EmployeeIdentification $identification,
        EmployeeName $name,
        EmployeeLastname $lastname = new EmployeeLastname,
        EmployeeState $state = new EmployeeState,
        EmployeeCreatedAt $createdAt = new EmployeeCreatedAt
    ): Employee {

        $employee = new Employee(
            $id,
            $identification,
            $name
        );

        $employee->setLastname($lastname);
        $employee->setCreatedAt($createdAt);
        $employee->setState($state);

        return $employee;
    }

    public function buildEmployeeId(?int $id = null): EmployeeId
    {
        return new EmployeeId($id);
    }

    public function buildEmployeeIdentification(string $identification): EmployeeIdentification
    {
        return new EmployeeIdentification($identification);
    }

    public function buildEmployeeName(string $name): EmployeeName
    {
        return new EmployeeName($name);
    }

    public function buildEmployeeLastname(string $lastname): EmployeeLastname
    {
        return new EmployeeLastname($lastname);
    }

    public function buildEmployeePhone(?string $phone = null): EmployeePhone
    {
        return new EmployeePhone($phone);
    }

    public function buildEmployeeEmail(?string $email = null): EmployeeEmail
    {
        return new EmployeeEmail($email);
    }

    public function buildEmployeeAddress(?string $address = null): EmployeeAddress
    {
        return new EmployeeAddress($address);
    }

    /**
     * @throws Exception
     */
    public function buildEmployeeState(int $state = ValueObjectStatus::STATE_NEW): EmployeeState
    {
        return new EmployeeState($state);
    }

    public function buildEmployeeCreatedAt(DateTime $datetime = new DateTime('now')): EmployeeCreatedAt
    {
        return new EmployeeCreatedAt($datetime);
    }

    public function buildEmployeeUpdatedAt(?DateTime $datetime = null): EmployeeUpdatedAt
    {
        return new EmployeeUpdatedAt($datetime);
    }

    public function buildEmployeeUserId(?int $id = null): EmployeeUserId
    {
        return new EmployeeUserId($id);
    }

    public function buildEmployeeSearch(?string $search = null): EmployeeSearch
    {
        return new EmployeeSearch($search);
    }

    public function buildEmployeeBirthdate(?DateTime $date = null): EmployeeBirthdate
    {
        return new EmployeeBirthdate($date);
    }

    public function buildEmployeeObservations(?string $observations = null): EmployeeObservations
    {
        return new EmployeeObservations($observations);
    }

    public function buildEmployeeIdentificationType(?string $type = null): EmployeeIdentificationType
    {
        return new EmployeeIdentificationType($type);
    }

    public function buildEmployeeImage(?string $image = null): EmployeeImage
    {
        return new EmployeeImage($image);
    }

    public function buildEmployees(Employee ...$employees): Employees
    {
        return new Employees($employees);
    }

    /**
     * @throws Exception
     */
    private function getDateTime(string $dateTime): DateTime
    {
        return new DateTime($dateTime);
    }

    public function buildEmployeeInstitutionId(?int $institutionId = null): EmployeeInstitutionId
    {
        return new EmployeeInstitutionId($institutionId);
    }
}
