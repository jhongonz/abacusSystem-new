<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 12:56:02
 */

namespace Core\Campus\Domain;

use Core\SharedContext\Model\ArrayIterator;

class CampusCollection extends ArrayIterator
{
    public const TYPE = 'campus-collection';

    public function __construct(Campus ...$campus)
    {
        foreach ($campus as $item) {
            $this->addItem($item);
        }
    }

    public function addItem(Campus $item): self
    {
        $this->items[] = $item;
        return $this;
    }

    public function items(): array
    {
        return $this->items;
    }

    public function addId(int $id): self
    {
        $this->aggregator[] = $id;
        return $this;
    }

    public function aggregator(): array
    {
        return $this->aggregator;
    }

    public function filters(): array
    {
        return $this->filters;
    }

    public function setFilters(array $filters): self
    {
        $this->filters = $filters;
        return $this;
    }
}
