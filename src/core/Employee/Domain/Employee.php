<?php

namespace Core\Employee\Domain;

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

class Employee
{
    public const TYPE = 'employee';
    private EmployeeId $id;
    private EmployeeUserId $userId;
    private EmployeeIdentification $identification;
    private EmployeeIdentificationType $identificationType;
    private EmployeeName $name;
    private EmployeeLastname $lastname;
    private EmployeePhone $phone;
    private EmployeeEmail $email;
    private EmployeeAddress $address;
    private EmployeeState $state;
    private EmployeeSearch $search;
    private EmployeeCreatedAt $createdAt;
    private EmployeeUpdateAt $updateAt;
    private EmployeeBirthdate $birthdate;
    private EmployeeObservations $observations;
    private EmployeeImage $image;

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

        $this->search = new EmployeeSearch();
        $this->updateAt = new EmployeeUpdateAt();
        $this->userId = new EmployeeUserId();
        $this->birthdate = new EmployeeBirthdate();
        $this->observations = new EmployeeObservations();
        $this->identificationType = new EmployeeIdentificationType();
        $this->image = new EmployeeImage();
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

    public function userId(): EmployeeUserId
    {
        return $this->userId;
    }

    public function setUserId(EmployeeUserId $id): self
    {
        $this->userId = $id;
        return $this;
    }

    public function search(): EmployeeSearch
    {
        return $this->search;
    }

    public function setSearch(EmployeeSearch $search): self
    {
        $this->search = $search;
        return $this;
    }

    public function refreshSearch(): self
    {
        $data = [
            $this->identification()->value(),
            $this->name()->value(),
            $this->lastname()->value(),
            $this->phone()->value(),
            $this->email()->value(),
            $this->address()->value(),
            $this->observations()->value(),
        ];

        $this->search()->setValue(implode(' ', $data));
        return $this;
    }

    public function birthdate(): EmployeeBirthdate
    {
        return $this->birthdate;
    }

    public function setBirthdate(EmployeeBirthdate $date): self
    {
        $this->birthdate = $date;
        return $this;
    }

    public function observations(): EmployeeObservations
    {
        return $this->observations;
    }

    public function setObservations(EmployeeObservations $observations): self
    {
        $this->observations = $observations;
        return $this;
    }

    public function identificationType(): EmployeeIdentificationType
    {
        return $this->identificationType;
    }

    public function setIdentificationType(EmployeeIdentificationType $type): self
    {
        $this->identificationType = $type;
        return $this;
    }

    public function image(): EmployeeImage
    {
        return $this->image;
    }

    public function setImage(EmployeeImage $image): self
    {
        $this->image = $image;
        return $this;
    }
}
