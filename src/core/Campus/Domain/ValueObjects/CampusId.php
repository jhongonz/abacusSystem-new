<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 12:14:14
 */

namespace Core\Campus\Domain\ValueObjects;

class CampusId
{
    private ?int $value;

    public function __construct(
        ?int $value = null,
    ) {
        if (!is_null($value)) {
            $this->validate($value);
        }

        $this->value = $value;
    }

    public function value(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->validate($value);
        $this->value = $value;

        return $this;
    }

    private function validate(int $value): void
    {
        if ($value < 1) {
            throw new \InvalidArgumentException(sprintf('<%s> does not allow the value <%d>.', static::class, $value));
        }
    }
}
