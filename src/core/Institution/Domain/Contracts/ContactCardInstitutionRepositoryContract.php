<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-16 09:18:36
 */

namespace Core\Institution\Domain\Contracts;

use Core\Institution\Domain\ContactCard;
use Core\Institution\Domain\ContactCards;
use Core\Institution\Domain\ValueObjectsContactCard\ContactId;
use Core\Institution\Domain\ValueObjectsContactCard\ContactInstitutionId;

interface ContactCardInstitutionRepositoryContract
{
    public function find(ContactId $id): ?ContactCard;

    public function getAll(ContactInstitutionId $institutionId, array $filters = []): ?ContactCards;

    public function delete(ContactId $id): void;

    public function persisContactCard(ContactCard $contactCard): ContactCard;
}
