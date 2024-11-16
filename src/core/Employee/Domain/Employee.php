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
use Core\Employee\Domain\ValueObjects\EmployeeInstitutionId;
use Core\Employee\Domain\ValueObjects\EmployeeLastname;
use Core\Employee\Domain\ValueObjects\EmployeeName;
use Core\Employee\Domain\ValueObjects\EmployeeObservations;
use Core\Employee\Domain\ValueObjects\EmployeePhone;
use Core\Employee\Domain\ValueObjects\EmployeeSearch;
use Core\Employee\Domain\ValueObjects\EmployeeState;
use Core\Employee\Domain\ValueObjects\EmployeeUpdatedAt;
use Core\Employee\Domain\ValueObjects\EmployeeUserId;

class Employee
{
    public const TYPE = 'employee';

    private EmployeeUserId $userId;
    private EmployeeInstitutionId $institutionId;
    private EmployeeIdentificationType $identificationType;
    private EmployeeSearch $search;
    private EmployeeUpdatedAt $updateAt;
    private EmployeeBirthdate $birthdate;
    private EmployeeObservations $observations;
    private EmployeeImage $image;

    public function __construct(
        private EmployeeId $id,
        private EmployeeIdentification $identification,
        private EmployeeName $name,
        private EmployeeLastname $lastname = new EmployeeLastname(),
        private EmployeeState $state = new EmployeeState(),
        private EmployeePhone $phone = new EmployeePhone(),
        private EmployeeEmail $email = new EmployeeEmail(),
        private EmployeeAddress $address = new EmployeeAddress(),
        private EmployeeCreatedAt $createdAt = new EmployeeCreatedAt(),
    ) {
        $this->search = new EmployeeSearch();
        $this->updateAt = new EmployeeUpdatedAt();
        $this->userId = new EmployeeUserId();
        $this->institutionId = new EmployeeInstitutionId();
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

    public function updatedAt(): EmployeeUpdatedAt
    {
        return $this->updateAt;
    }

    public function setUpdatedAt(EmployeeUpdatedAt $updateAt): self
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

    /**
     * @return $this
     */
    public function refreshSearch(): self
    {
        $data = [
            $this->identification->value(),
            $this->name->value(),
            $this->lastname->value(),
            $this->phone->value(),
            $this->email->value(),
            $this->address->value(),
            $this->observations->value(),
        ];

        $dataSearch = trim(strtolower(implode(' ', $data)));
        $this->search->setValue($dataSearch);

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

    public function institutionId(): EmployeeInstitutionId
    {
        return $this->institutionId;
    }

    public function setInstitutionId(EmployeeInstitutionId $institutionId): self
    {
        $this->institutionId = $institutionId;

        return $this;
    }
}
