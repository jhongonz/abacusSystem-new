<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-15 20:21:57
 */

namespace Core\Institution\Domain;

use Core\Institution\Domain\ValueObjectsContactCard\ContactCreatedAt;
use Core\Institution\Domain\ValueObjectsContactCard\ContactDefault;
use Core\Institution\Domain\ValueObjectsContactCard\ContactEmail;
use Core\Institution\Domain\ValueObjectsContactCard\ContactId;
use Core\Institution\Domain\ValueObjectsContactCard\ContactInstitutionId;
use Core\Institution\Domain\ValueObjectsContactCard\ContactObservations;
use Core\Institution\Domain\ValueObjectsContactCard\ContactPerson;
use Core\Institution\Domain\ValueObjectsContactCard\ContactPhone;
use Core\Institution\Domain\ValueObjectsContactCard\ContactSearch;
use Core\Institution\Domain\ValueObjectsContactCard\ContactState;
use Core\Institution\Domain\ValueObjectsContactCard\ContactUpdatedAt;

class ContactCard
{
    public const TYPE = 'contact-card-institution';

    private ContactId $id;
    private ContactInstitutionId $institutionId;
    private ContactPhone $phone;
    private ContactEmail $email;
    private ContactPerson $person;
    private ContactDefault $default;
    private ContactObservations $observations;
    private ContactSearch $search;
    private ContactState $state;
    private ContactCreatedAt $createdAt;
    private ContactUpdatedAt $updatedAt;

    public function __construct(
        ContactId $id,
        ContactInstitutionId $institutionId,
        ContactPhone $phone,
        ContactEmail $email
    ) {
        $this->id = $id;
        $this->institutionId = $institutionId;
        $this->phone = $phone;
        $this->email = $email;

        $this->person = new ContactPerson;
        $this->default = new ContactDefault;
        $this->observations = new ContactObservations;
        $this->search = new ContactSearch;
        $this->state = new ContactState;
        $this->createdAt = new ContactCreatedAt;
        $this->updatedAt = new ContactUpdatedAt;
    }

    public function id(): ContactId
    {
        return $this->id;
    }

    public function setId(ContactId $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function institutionId(): ContactInstitutionId
    {
        return $this->institutionId;
    }

    public function setInstitutionId(ContactInstitutionId $institutionId): self
    {
        $this->institutionId = $institutionId;
        return $this;
    }

    public function phone(): ContactPhone
    {
        return $this->phone;
    }

    public function setPhone(ContactPhone $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function email(): ContactEmail
    {
        return $this->email;
    }

    public function setEmail(ContactEmail $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function person(): ContactPerson
    {
        return $this->person;
    }

    public function setPerson(ContactPerson $person): self
    {
        $this->person = $person;
        return $this;
    }

    public function contactDefault(): ContactDefault
    {
        return $this->default;
    }

    public function setContactDefault(ContactDefault $default): self
    {
        $this->default = $default;
        return $this;
    }

    public function observations(): ContactObservations
    {
        return $this->observations;
    }

    public function setObservations(ContactObservations $observations): self
    {
        $this->observations = $observations;
        return $this;
    }

    public function search(): ContactSearch
    {
        return $this->search;
    }

    public function setSearch(ContactSearch $search): self
    {
        $this->search = $search;
        return $this;
    }

    public function state(): ContactState
    {
        return $this->state;
    }

    public function setState(ContactState $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function createdAt(): ContactCreatedAt
    {
        return $this->createdAt;
    }

    public function setCreatedAt(ContactCreatedAt $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function updatedAt(): ContactUpdatedAt
    {
        return $this->updatedAt;
    }

    public function setUpdateAt(ContactUpdatedAt $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
