<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-15 22:03:58
 */

namespace Core\Institution\Domain\Contracts;

use Core\Institution\Domain\ContactCard;

interface ContactCardInstitutionDataTransformerContract
{
    public function write(ContactCard $contactCard): ContactCardInstitutionDataTransformerContract;

    public function read(): array;
}
