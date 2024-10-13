<?php

namespace Core\Profile\Domain;

use Core\SharedContext\Model\ArrayIterator;

class Modules extends ArrayIterator
{
    public const TYPE = 'modules';

    public function __construct(array $modules = [])
    {
        foreach ($modules as $module) {
            $this->validateInstanceElement(Module::class, $module);
        }

        parent::__construct($modules);
    }

    public function addItem(Module $item): self
    {
        $this->validateInstanceElement(Module::class, $item);
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

    public function moduleElementsOfKey(string $menuKey): array
    {
        $this->rewind();
        $modulesKeys = [];

        /** @var Module $item */
        foreach ($this as $item) {
            if ($item->menuKey()->value() === $menuKey) {
                $modulesKeys[] = $item;
            }
        }

        return $modulesKeys;
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
