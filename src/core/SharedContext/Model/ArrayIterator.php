<?php

namespace Core\SharedContext\Model;

use Countable;
use Iterator;
use ReturnTypeWillChange;

/**
 * @codeCoverageIgnore
 */
abstract class ArrayIterator implements Iterator, Countable
{
    protected array $items = [];
    protected array $aggregator = [];

    abstract public function addItem(mixed $item): ArrayIterator;
    abstract public function items(): array;
    abstract public function addId(int $id): ArrayIterator;
    abstract public function aggregator(): array;

    public function current(): mixed
    {
        return \current($this->items);
    }

    #[ReturnTypeWillChange] public function next()
    {
        return \next($this->items);
    }

    #[ReturnTypeWillChange] public function key()
    {
        return \key($this->items);
    }

    public function valid(): bool
    {
        return false !== \current($this->items);
    }

    #[ReturnTypeWillChange] public function rewind()
    {
        return \reset($this->items);
    }

    public function count(): int
    {
        return \count($this->items);
    }
}
