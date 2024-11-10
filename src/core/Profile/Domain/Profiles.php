<?php

namespace Core\Profile\Domain;

use Core\SharedContext\Model\ArrayIterator;

class Profiles extends ArrayIterator
{
    public const TYPE = 'profiles';

    /**
     * @param array<int, mixed> $profiles
     */
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

    /**
     * @return array<int, mixed>
     */
    public function items(): array
    {
        return $this->getArrayCopy();
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

    public function addId(int $id): self
    {
        $this->aggregator[] = $id;

        return $this;
    }

    /**
     * @return array<int, int|null>
     */
    public function aggregator(): array
    {
        return $this->aggregator;
    }
}
