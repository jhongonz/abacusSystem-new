<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-15 21:18:01
 */

namespace Core\Institution\Application\Factory;

use Core\Institution\Domain\ContactCard;
use Core\Institution\Domain\ContactCards;
use Core\Institution\Domain\Contracts\ContactCardInstitutionFactoryContract;
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
use DateTime;
use Exception;

class ContactCardInstitutionFactory implements ContactCardInstitutionFactoryContract
{
    public function buildContactCard(
        ContactId $id,
        ContactInstitutionId $institutionId,
        ContactPhone $phone,
        ContactEmail $email
    ): ContactCard {

        return new ContactCard(
            $id,
            $institutionId,
            $phone,
            $email
        );
    }

    /**
     * @throws Exception
     */
    public function buildContactCardFromArray(array $data): ContactCard
    {
        $data = $data[ContactCard::TYPE];

        $contactCard = $this->buildContactCard(
            $this->buildContactId($data['id']),
            $this->buildContactInstitutionId($data['institutionId']),
            $this->buildContactPhone($data['phone']),
            $this->buildContactEmail($data['email'])
        );

        $contactCard->setPerson(
            $this->buildContactPerson($data['person'])
        );

        $contactCard->setContactDefault(
            $this->buildContactDefault($data['default'])
        );

        $contactCard->setObservations(
            $this->buildContactObservations($data['observations'])
        );

        $contactCard->setState(
            $this->buildContactState($data['state'])
        );

        $contactCard->setSearch(
            $this->buildContactSearch($data['search'])
        );

        $contactCard->setCreatedAt(
            $this->buildContactCreatedAt(
                new DateTime($data['createdAt']['date'])
            )
        );

        if (isset($data['updatedAt'])) {
            $contactCard->setUpdateAt(
                $this->buildContactUpdatedAt(
                    new DateTime($data['updatedAt']['date'])
                )
            );
        }

        return $contactCard;
    }

    public function buildContactId(?int $id): ContactId
    {
        return new ContactId($id);
    }

    public function buildContactInstitutionId(int $institutionId): ContactInstitutionId
    {
        return new ContactInstitutionId($institutionId);
    }

    public function buildContactPhone(string $phone): ContactPhone
    {
        return new ContactPhone($phone);
    }

    public function buildContactEmail(?string $email): ContactEmail
    {
        return new ContactEmail($email);
    }

    public function buildContactPerson(?string $person = null): ContactPerson
    {
        return new ContactPerson($person);
    }

    public function buildContactDefault(?bool $default = null): ContactDefault
    {
        return new ContactDefault($default);
    }

    public function buildContactObservations(?string $observations = null): ContactObservations
    {
        return new ContactObservations($observations);
    }

    public function buildContactSearch(?string $search = null): ContactSearch
    {
        return new ContactSearch($search);
    }

    /**
     * @throws Exception
     */
    public function buildContactState(?int $state = null): ContactState
    {
        return new ContactState($state);
    }

    public function buildContactCreatedAt(?DateTime $dateTime = null): ContactCreatedAt
    {
        return new ContactCreatedAt($dateTime);
    }

    public function buildContactUpdatedAt(?DateTime $dateTime = null): ContactUpdatedAt
    {
        return new ContactUpdatedAt($dateTime);
    }

    public function buildContactCards(ContactCard ...$contactCard): ContactCards
    {
        return new ContactCards(... $contactCard);
    }
}
