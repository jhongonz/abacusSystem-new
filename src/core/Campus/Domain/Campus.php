<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 12:26:54
 */

namespace Core\Campus\Domain;

use Core\Campus\Domain\ValueObjects\CampusAddress;
use Core\Campus\Domain\ValueObjects\CampusCreatedAt;
use Core\Campus\Domain\ValueObjects\CampusEmail;
use Core\Campus\Domain\ValueObjects\CampusId;
use Core\Campus\Domain\ValueObjects\CampusInstitutionId;
use Core\Campus\Domain\ValueObjects\CampusName;
use Core\Campus\Domain\ValueObjects\CampusObservations;
use Core\Campus\Domain\ValueObjects\CampusPhone;
use Core\Campus\Domain\ValueObjects\CampusSearch;
use Core\Campus\Domain\ValueObjects\CampusState;
use Core\Campus\Domain\ValueObjects\CampusUpdatedAt;

class Campus
{
    public const TYPE = 'campus';

    private CampusAddress $address;
    private CampusPhone $phone;
    private CampusEmail $email;
    private CampusObservations $observations;
    private CampusSearch $search;
    private CampusUpdatedAt $updatedAt;

    public function __construct(
        private CampusId $id,
        private CampusInstitutionId $institutionId,
        private CampusName $name,
        private CampusState $state = new CampusState(),
        private CampusCreatedAt $createdAt = new CampusCreatedAt(),
    ) {
        $this->address = new CampusAddress();
        $this->phone = new CampusPhone();
        $this->email = new CampusEmail();
        $this->observations = new CampusObservations();
        $this->search = new CampusSearch();
        $this->updatedAt = new CampusUpdatedAt();
    }

    public function id(): CampusId
    {
        return $this->id;
    }

    public function setId(CampusId $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function institutionId(): CampusInstitutionId
    {
        return $this->institutionId;
    }

    public function setInstitutionId(CampusInstitutionId $institutionId): self
    {
        $this->institutionId = $institutionId;

        return $this;
    }

    public function name(): CampusName
    {
        return $this->name;
    }

    public function setName(CampusName $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function address(): CampusAddress
    {
        return $this->address;
    }

    public function setAddress(CampusAddress $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function phone(): CampusPhone
    {
        return $this->phone;
    }

    public function setPhone(CampusPhone $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function email(): CampusEmail
    {
        return $this->email;
    }

    public function setEmail(CampusEmail $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function observations(): CampusObservations
    {
        return $this->observations;
    }

    public function setObservations(CampusObservations $observations): self
    {
        $this->observations = $observations;

        return $this;
    }

    public function search(): CampusSearch
    {
        return $this->search;
    }

    public function setSearch(CampusSearch $search): self
    {
        $this->search = $search;

        return $this;
    }

    public function state(): CampusState
    {
        return $this->state;
    }

    public function setState(CampusState $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function createdAt(): CampusCreatedAt
    {
        return $this->createdAt;
    }

    public function setCreatedAt(CampusCreatedAt $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function updatedAt(): CampusUpdatedAt
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(CampusUpdatedAt $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function refreshSearch(): self
    {
        /** @var string $address */
        $address = $this->address->value();

        /** @var string $phone */
        $phone = $this->phone->value();

        /** @var string $email */
        $email = $this->email->value();

        /** @var string $observations */
        $observations = $this->observations->value();

        $data = [
            trim(strtolower($this->name->value())),
            trim(strtolower($address)),
            trim(strtolower($phone)),
            trim(strtolower($email)),
            trim(strtolower($observations)),
        ];

        $dataSearch = implode(' ', $data);
        $this->search->setValue($dataSearch);

        return $this;
    }
}
