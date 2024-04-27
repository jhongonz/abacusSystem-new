<?php

namespace Core\Employee\Application\Factory;

use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\ValueObjects\EmployeeAddress;
use Core\Employee\Domain\ValueObjects\EmployeeBirthdate;
use Core\Employee\Domain\ValueObjects\EmployeeCreatedAt;
use Core\Employee\Domain\ValueObjects\EmployeeEmail;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use Core\Employee\Domain\ValueObjects\EmployeeIdentificationType;
use Core\Employee\Domain\ValueObjects\EmployeeImage;
use Core\Employee\Domain\ValueObjects\EmployeeLastname;
use Core\Employee\Domain\ValueObjects\EmployeeName;
use Core\Employee\Domain\ValueObjects\EmployeeObservations;
use Core\Employee\Domain\ValueObjects\EmployeePhone;
use Core\Employee\Domain\ValueObjects\EmployeeSearch;
use Core\Employee\Domain\ValueObjects\EmployeeState;
use Core\Employee\Domain\ValueObjects\EmployeeUpdateAt;
use Core\Employee\Domain\ValueObjects\EmployeeUserId;
use DateTime;
use Exception;

class EmployeeFactory implements EmployeeFactoryContract
{
    /**
     * @throws Exception
     */
    public function buildEmployeeFromArray(array $data): Employee
    {
        $data = $data[Employee::TYPE];
        $employee = $this->buildEmployee(
            $this->buildEmployeeId($data['id']),
            $this->buildEmployeeIdentification($data['identification']),
            $this->buildEmployeeName($data['name']),
            $this->buildEmployeeLastname($data['lastname']),
            $this->buildEmployeeState($data['state']),
            $this->buildEmployeeCreatedAt(
                new DateTime($data['createdAt']['date'])
            ),
        );

        $employee->setIdentificationType($this->buildEmployeeIdentificationType($data['identification_type']));
        $employee->setUserId($this->buildEmployeeUserId($data['userId']));
        $employee->setAddress($this->buildEmployeeAddress($data['address']));
        $employee->setPhone($this->buildEmployeePhone($data['phone']));
        $employee->setEmail($this->buildEmployeeEmail($data['email']));

        if (!is_null($data['updatedAt'])) {
            $employee->setUpdatedAt($this->buildEmployeeUpdatedAt(
                new DateTime($data['updatedAt']['date'])
            ));
        }

        if (!is_null($data['birthdate'])) {
            $employee->setBirthdate($this->buildEmployeeBirthdate(
                new DateTime($data['birthdate']['date'])
            ));
        }

        $employee->setObservations($this->buildEmployeeObservations($data['observations']));
        $employee->setImage($this->buildEmployeeImage($data['image']));

        if (in_array('search', $data)) {
            $employee->setSearch($this->buildEmployeeSearch($data['search']));
        }

        return $employee;
    }

    public function buildEmployee(
        EmployeeId $id,
        EmployeeIdentification $identification,
        EmployeeName $name,
        EmployeeLastname $lastname = new EmployeeLastname(),
        EmployeeState $state = new EmployeeState(),
        EmployeeCreatedAt $createdAt = new EmployeeCreatedAt()
    ): Employee {

        $employee = new Employee(
            $id,
            $identification,
            $name
         );

        if (!is_null($lastname)) $employee->setLastname($lastname);
        if (!is_null($createdAt)) $employee->setCreatedAt($createdAt);
        if (!is_null($state)) $employee->setState($state);

        return $employee;
    }

    public function buildEmployeeId(null|int $id = null): EmployeeId
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

    public function buildEmployeePhone(string $phone): EmployeePhone
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
    public function buildEmployeeState(?int $state = null): EmployeeState
    {
        return new EmployeeState($state);
    }

    public function buildEmployeeCreatedAt(?DateTime $datetime = null): EmployeeCreatedAt
    {
        return new EmployeeCreatedAt($datetime);
    }

    public function buildEmployeeUpdatedAt(?DateTime $datetime = null): EmployeeUpdateAt
    {
        return new EmployeeUpdateAt($datetime);
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
}
