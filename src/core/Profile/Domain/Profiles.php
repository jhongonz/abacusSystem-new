<?php

namespace Core\Profile\Domain;

use Core\SharedContext\Model\ArrayIterator;

class Profiles extends ArrayIterator
{
    public const TYPE = 'profiles';

    public function __construct(Profile ...$profiles)
    {
        foreach ($profiles as $profile) {
            $this->addItem($profile);
        }
    }

    public function addItem(Profile $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    public function items(): array
    {
        return $this->items;
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

    public function addId(int $id): self
    {
        $this->aggregator[] = $id;

        return $this;
    }

    public function aggregator(): array
    {
        return $this->aggregator;
    }
}
