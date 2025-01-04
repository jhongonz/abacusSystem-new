<?php

namespace Core\Profile\Domain;

use Core\SharedContext\Model\ArrayIterator;

class Profiles extends ArrayIterator
{
    public const TYPE = 'profiles';

    /**
     * @param array<int|string, Profile> $profiles
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
        $this->append($item);

        return $this;
    }

    /**
     * @return array<int|string, mixed>
     */
    public function items(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * @return array<int|string, mixed>
     */
    public function filters(): array
    {
        return $this->filters;
    }

    /**
     * @param array<int|string, mixed> $filters
     *
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
