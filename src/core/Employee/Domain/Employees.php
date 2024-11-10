<?php

namespace Core\Employee\Domain;

use Core\SharedContext\Model\ArrayIterator;

class Employees extends ArrayIterator
{
    public const TYPE = 'employees';

    /**
     * @param array<string, mixed> $employees
     */
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

    /**
     * @return array<string, mixed>
     */
    public function items(): array
    {
        return $this->getArrayCopy();
    }

    public function addId(int $id): self
    {
        $this->aggregator[] = $id;
        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function aggregator(): array
    {
        return $this->aggregator;
    }

    /**
     * @return array<string, mixed>
     */
    public function filters(): array
    {
        return $this->filters;
    }

    /**
     * @param array<string, mixed> $filters
     * @return $this
     */
    public function setFilters(array $filters): self
    {
        $this->filters = $filters;
        return $this;
    }
}
