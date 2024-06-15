<?php

namespace Core\SharedContext\Model;

use Countable;
use Iterator;
use ReturnTypeWillChange;

/**
 * @codeCoverageIgnore
 */
abstract class ArrayIterator implements Countable, Iterator
{
    protected array $items = [];

    protected array $aggregator = [];

    protected array $filters = [];

    abstract public function items(): array;

    abstract public function addId(int $id): ArrayIterator;

    abstract public function aggregator(): array;

    abstract public function filters(): array;
    abstract public function setFilters(array $filters): ArrayIterator;

    public function current(): mixed
    {
        return \current($this->items);
    }

    #[ReturnTypeWillChange]
    public function next()
    {
        return \next($this->items);
    }

    #[ReturnTypeWillChange]
    public function key()
    {
        return \key($this->items);
    }

    public function valid(): bool
    {
        return \current($this->items) !== false;
    }

    #[ReturnTypeWillChange]
    public function rewind()
    {
        return \reset($this->items);
    }

    public function count(): int
    {
        return \count($this->items);
    }
}
