<?php

namespace Core\Employee\Domain;

use Core\SharedContext\Model\ArrayIterator;

class Employees extends ArrayIterator
{
    public const TYPE = 'employees';

    private array $filters;

    public function __construct(Employee ...$employees)
    {
        foreach ($employees as $employee) {
            $this->addItem($employee);
        }

        $this->filters = [];
    }

    /**
     * @param  Employee  $item
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
