<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-15 21:15:45
 */

namespace Core\Institution\Domain\Contracts;

use Core\Institution\Domain\ContactCard;
use Core\Institution\Domain\ContactCards;
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

interface ContactCardInstitutionFactoryContract
{
    public function buildContactCard(
        ContactId $id,
        ContactInstitutionId $institutionId,
        ContactPhone $phone,
        ContactEmail $email
    ): ContactCard;

    public function buildContactCardFromArray(array $data): ContactCard;

    public function buildContactId(?int $id): ContactId;

    public function buildContactInstitutionId(int $institutionId): ContactInstitutionId;

    public function buildContactPhone(string $phone): ContactPhone;

    public function buildContactEmail(?string $email): ContactEmail;

    public function buildContactPerson(?string $person = null): ContactPerson;

    public function buildContactDefault(?bool $default = null): ContactDefault;

    public function buildContactObservations(?string $observations = null): ContactObservations;

    public function buildContactSearch(?string $search = null): ContactSearch;

    public function buildContactState(?int $state = null): ContactState;

    public function buildContactCreatedAt(?\DateTime $dateTime = null): ContactCreatedAt;

    public function buildContactUpdatedAt(?\DateTime $dateTime = null): ContactUpdatedAt;

    public function buildContactCards(ContactCard ...$contactCard): ContactCards;
}
