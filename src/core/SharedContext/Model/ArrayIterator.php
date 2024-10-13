<?php

namespace Core\SharedContext\Model;

abstract class ArrayIterator extends \ArrayIterator
{
    protected array $items = [];

    protected array $aggregator = [];

    protected array $filters = [];

    abstract public function items(): array;

    abstract public function addId(int $id): ArrayIterator;

    abstract public function aggregator(): array;

    abstract public function filters(): array;
    abstract public function setFilters(array $filters): ArrayIterator;

    protected function validateInstanceElement(string $class, $item): void
    {
        if (!$item instanceof $class) {
            throw new \InvalidArgumentException('Item is not valid to collection '. get_class($this));
        }
    }
}
