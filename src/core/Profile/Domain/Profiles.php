<?php

namespace Core\Profile\Domain;

use Core\SharedContext\Model\ArrayIterator;

class Profiles extends ArrayIterator
{
    public const TYPE = 'profiles';

    public function __construct(array $profiles = [])
    {
        foreach ($profiles as $profile) {
            $this->validateInstanceElement(Profile::class, $profile);
        }

        parent::__construct($profiles);
    }

    public function addItem(Profile $item): self
    {
        $this->validateInstanceElement(Profile::class, $item);
        $this->append($item);

        return $this;
    }

    public function items(): array
    {
        return $this->getArrayCopy();
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
