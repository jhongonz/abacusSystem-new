<?php

namespace Core\Employee\Domain;

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

class Employee
{
    public const TYPE = 'employee';
    private EmployeeId $id;
    private EmployeeIdentification $identification;
    private EmployeeName $name;
    private EmployeeLastname $lastname;
    private EmployeePhone $phone;
    private EmployeeEmail $email;
    private EmployeeAddress $address;
    private EmployeeState $state;
    private EmployeeCreatedAt $createdAt;
    private EmployeeUpdateAt $updateAt;

    public function __construct(
        EmployeeId $id,
        EmployeeIdentification $identification,
        EmployeeName $name,
        EmployeeLastname $lastname = new EmployeeLastname(),
        EmployeeState $state = new EmployeeState(),
        EmployeePhone $phone = new EmployeePhone(),
        EmployeeEmail $email = new EmployeeEmail(),
        EmployeeAddress $address = new EmployeeAddress(),
        EmployeeCreatedAt $createdAt = new EmployeeCreatedAt()
    ) {
        $this->id = $id;
        $this->identification = $identification;
        $this->name = $name;
        $this->lastname = $lastname;
        $this->state = $state;
        $this->phone = $phone;
        $this->email = $email;
        $this->address = $address;
        $this->createdAt = $createdAt;

        $this->updateAt = new EmployeeUpdateAt();
    }

    public function id(): EmployeeId
    {
        return $this->id;
    }

    public function setId(EmployeeId $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function identification(): EmployeeIdentification
    {
        return $this->identification;
    }

    public function setIdentification(EmployeeIdentification $identification): self
    {
        $this->identification = $identification;
        return $this;
    }

    public function name(): EmployeeName
    {
        return $this->name;
    }

    public function setName(EmployeeName $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function lastname(): EmployeeLastname
    {
        return $this->lastname;
    }

    public function setLastname(EmployeeLastname $lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function phone(): EmployeePhone
    {
        return $this->phone;
    }

    public function setPhone(EmployeePhone $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function email(): EmployeeEmail
    {
        return $this->email;
    }

    public function setEmail(EmployeeEmail $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function address(): EmployeeAddress
    {
        return $this->address;
    }

    public function setAddress(EmployeeAddress $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function state(): EmployeeState
    {
        return $this->state;
    }

    public function setState(EmployeeState $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function createdAt(): EmployeeCreatedAt
    {
        return $this->createdAt;
    }

    public function setCreatedAt(EmployeeCreatedAt $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function updatedAt():EmployeeUpdateAt
    {
        return $this->updateAt;
    }

    public function setUpdatedAt(EmployeeUpdateAt $updateAt): self
    {
        $this->updateAt = $updateAt;
        return $this;
    }
}
