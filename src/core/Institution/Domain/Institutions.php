<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-19 22:22:41
 */

namespace Core\Institution\Domain;

use Core\SharedContext\Model\ArrayIterator;

class Institutions extends ArrayIterator
{
    public const TYPE = 'institutions';

    public function __construct(Institution ...$institutions)
    {
        foreach ($institutions as $institution) {
            $this->addItem($institution);
        }
    }

    /**
     * @param Institution $item
     * @return $this
     */
    public function addItem($item): self
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
