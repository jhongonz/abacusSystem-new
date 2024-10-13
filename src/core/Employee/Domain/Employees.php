<?php

namespace Core\Employee\Domain;

use Core\SharedContext\Model\ArrayIterator;

class Employees extends ArrayIterator
{
    public const TYPE = 'employees';

    public function __construct(array $employees = [])
    {
        foreach ($employees as $employee) {
            $this->validateInstanceElement(Employee::class, $employee);
        }

        parent::__construct($employees);
    }

    public function addItem(Employee $item): self
    {
        $this->validateInstanceElement(Employee::class, $item);

        $this->append($item);
        return $this;
    }

    public function items(): array
    {
        return $this->getArrayCopy();
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
