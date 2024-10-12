<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 12:56:02
 */

namespace Core\Campus\Domain;

class CampusCollection extends \ArrayIterator
{
    public const TYPE = 'campus-collection';
    private array $aggregator = [];
    private array $filters = [];

    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->validateInstanceElement($item);
        }

        parent::__construct($items);
    }

    public function addItem(Campus $item): self
    {
        $this->validateInstanceElement($item);

        $this->append($item);
        return $this;
    }

    public function items(): array
    {
        return $this->getArrayCopy();
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

    public function filters(): array
    {
        return $this->filters;
    }

    public function setFilters(array $filters): self
    {
        $this->filters = $filters;
        return $this;
    }

    private function validateInstanceElement($item): void
    {
        if (!$item instanceof Campus) {
            throw new \InvalidArgumentException('Item is not valid to collection '.self::class);
        }
    }
}
