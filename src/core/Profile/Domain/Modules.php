<?php

namespace Core\Profile\Domain;

use Core\SharedContext\Model\ArrayIterator;

class Modules extends ArrayIterator
{
    public const TYPE = 'modules';

    /**
     * @param array<int|string, Module> $modules
     */
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

    /**
     * @return array<Module>
     */
    public function moduleElementsOfKey(string $menuKey): array
    {
        $this->rewind();
        $modulesKeys = [];

        /** @var Module $module */
        foreach ($this as $module) {
            if ($module->menuKey()->value() === $menuKey) {
                $modulesKeys[] = $module;
            }
        }

        return $modulesKeys;
    }

    public function addId(int $id): self
    {
        $this->aggregator[] = $id;

        return $this;
    }

    /**
     * @return array<int, int>
     */
    public function aggregator(): array
    {
        return $this->aggregator;
    }
}
