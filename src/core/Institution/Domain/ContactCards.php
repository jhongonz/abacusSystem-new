<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-15 20:24:52
 */

namespace Core\Institution\Domain;

use Core\SharedContext\Model\ArrayIterator;

class ContactCards extends ArrayIterator
{
    public const TYPE = 'contact-cards-institution';

    public function __construct(ContactCard... $contactCards)
    {
        foreach ($contactCards as $contactCard) {
            $this->addItem($contactCard);
        }
    }

    public function addItem(ContactCard $item): ArrayIterator
    {
        $this->items[] = $item;
        return $this;
    }

    public function items(): array
    {
        return $this->items;
    }

    public function addId(int $id): ArrayIterator
    {
        $this->aggregator[] = $id;
    }

    public function aggregator(): array
    {
        return $this->aggregator;
    }

    public function filters(): array
    {
        return $this->filters;
    }

    public function setFilters(array $filters): ArrayIterator
    {
        $this->filters = $filters;
        return $this;
    }
}
