<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 12:56:02
 */

namespace Core\Campus\Domain;

use Core\SharedContext\Model\ArrayIterator;

class CampusCollection extends ArrayIterator
{
    public const TYPE = 'campus-collection';

    /**
     * @param array<string, mixed> $items
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->validateInstanceElement(Campus::class, $item);
        }

        parent::__construct($items);
    }

    public function addItem(Campus $item): self
    {
        $this->validateInstanceElement(Campus::class, $item);

        $this->append($item);
        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function items(): array
    {
        return $this->getArrayCopy();
    }

    public function addId(int $id): self
    {
        $this->aggregator[] = $id;
        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function aggregator(): array
    {
        return $this->aggregator;
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
}
