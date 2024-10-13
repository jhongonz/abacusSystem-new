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

    public function __construct(array $institutions = [])
    {
        foreach ($institutions as $institution) {
            $this->validateInstanceElement(Institution::class, $institution);
        }

        parent::__construct($institutions);
    }

    public function addItem(Institution $item): self
    {
        $this->validateInstanceElement(Institution::class, $item);

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
