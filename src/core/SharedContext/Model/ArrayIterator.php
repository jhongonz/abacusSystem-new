<?php

namespace Core\SharedContext\Model;

/**
 * @extends \ArrayIterator<int|string, mixed>
 */
abstract class ArrayIterator extends \ArrayIterator
{
    /**
     * @var array<int|string, mixed> $items
     */
    protected array $items = [];

    /**
     * @var array<int, int>
     */
    protected array $aggregator = [];

    /**
     * @var array<int|string, mixed> $filters
     */
    protected array $filters = [];

    /**
     * @return array<int|string, mixed>
     */
    abstract public function items(): array;

    abstract public function addId(int $id): ArrayIterator;

    /**
     * @return array<int, int>
     */
    abstract public function aggregator(): array;

    /**
     * @return array<int|string, mixed>
     */
    abstract public function filters(): array;

    /**
     * @param array<int|string, mixed> $filters
     * @return ArrayIterator
     */
    abstract public function setFilters(array $filters): ArrayIterator;

    protected function validateInstanceElement(string $class, mixed $item): void
    {
        if (!$item instanceof $class) {
            throw new \InvalidArgumentException('Item is not valid to collection '. get_class($this));
        }
    }
}
