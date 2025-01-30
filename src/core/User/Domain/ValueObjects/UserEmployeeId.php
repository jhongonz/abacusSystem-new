<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Domain\ValueObjects;

class UserEmployeeId
{
    private ?int $value = null;

    public function __construct(?int $value = null)
    {
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

    private function validate(int $id): void
    {
        if ($id < 1) {
            throw new \InvalidArgumentException(sprintf('<%s> does not allow the value <%s>.', static::class, $id));
        }
    }
}
