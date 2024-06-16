<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-16 09:56:12
 */

namespace Core\Institution\Infrastructure\Persistence\Translators;

use Core\Institution\Domain\ContactCard;
use Core\Institution\Domain\ContactCards;
use Core\Institution\Domain\Contracts\ContactCardInstitutionFactoryContract;
use Core\Institution\Infrastructure\Persistence\Eloquent\Model\InstitutionContactCard as ContactCardModel;
use Exception;

class ContactCardInstitutionTranslator
{
    private ContactCardInstitutionFactoryContract $cardInstitutionFactory;
    private ContactCardModel $contactCard;
    private array $collection;

    public function __construct(
        ContactCardInstitutionFactoryContract $cardInstitutionFactory
    ) {
        $this->cardInstitutionFactory = $cardInstitutionFactory;
        $this->collection = [];
    }

    public function setModel(ContactCardModel $model): self
    {
        $this->contactCard = $model;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function toDomain(): ContactCard
    {
        $contactCard = $this->cardInstitutionFactory->buildContactCard(
            $this->cardInstitutionFactory->buildContactId($this->contactCard->id()),
            $this->cardInstitutionFactory->buildContactInstitutionId($this->contactCard->institutionId()),
            $this->cardInstitutionFactory->buildContactPhone($this->contactCard->phone()),
            $this->cardInstitutionFactory->buildContactEmail($this->contactCard->email())
        );

        $contactCard->setObservations($this->cardInstitutionFactory->buildContactObservations($this->contactCard->observations()));
        $contactCard->setContactDefault($this->cardInstitutionFactory->buildContactDefault($this->contactCard->contactDefault()));
        $contactCard->setPerson($this->cardInstitutionFactory->buildContactPerson($this->contactCard->contactPerson()));
        $contactCard->setState($this->cardInstitutionFactory->buildContactState($this->contactCard->state()));
        $contactCard->setSearch($this->cardInstitutionFactory->buildContactSearch($this->contactCard->search()));
        $contactCard->setCreatedAt($this->cardInstitutionFactory->buildContactCreatedAt($this->contactCard->createdAt()));
        $contactCard->setUpdateAt($this->cardInstitutionFactory->buildContactUpdatedAt($this->contactCard->updatedAt()));

        return $contactCard;
    }

    public function setCollection(array $collection): self
    {
        $this->collection = $collection;
        return $this;
    }

    public function toDomainCollection(): ContactCards
    {
        $contactCards = new ContactCards;
        foreach ($this->collection as $id) {
            $contactCards->addId($id);
        }

        return $contactCards;
    }
}
