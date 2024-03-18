<?php

namespace Core\Profile\Domain;

use Core\SharedContext\Model\ArrayIterator;

class Modules extends ArrayIterator
{
    public const TYPE = 'modules';
    private array $filters;
    
    public function __construct(Module ...$modules)
    {
        foreach ($modules as $module) {
            $this->addItem($module);
        }
        
        $this->filters = [];
    }

    /**
     * @param Module $item
     * @return self
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
        
        /**@var Module $item */
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