<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-15 22:06:35
 */

namespace Core\Institution\Application\DataTransformer;

use Core\Institution\Domain\ContactCard;
use Core\Institution\Domain\Contracts\ContactCardInstitutionDataTransformerContract;

class ContactCardInstitutionDataTransformer implements ContactCardInstitutionDataTransformerContract
{
    private ContactCard $contactCard;

    public function write(ContactCard $contactCard): self
    {
        $this->contactCard = $contactCard;
        return $this;
    }

    public function read(): array
    {
        return [
            ContactCard::TYPE => [
                'id' => $this->contactCard->id()->value(),
                'institutionId' => $this->contactCard->institutionId()->value(),
                'phone' => $this->contactCard->phone()->value(),
                'email' => $this->contactCard->email()->value(),
                'person' => $this->contactCard->person()->value(),
                'default' => $this->contactCard->contactDefault()->value(),
                'observations' => $this->contactCard->observations()->value(),
                'search' => $this->contactCard->search()->value(),
                'state' => $this->contactCard->state()->value(),
                'createdAt' => $this->contactCard->createdAt()->value(),
                'updatedAt' => $this->contactCard->updatedAt()->value()
            ]
        ];
    }
}
