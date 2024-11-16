<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Domain\ValueObjects;

use InvalidArgumentException;

class UserEmployeeId
{
    public function __construct(
        private ?int $value = null
    ) {
        if (! is_null($value)) {
            $this->validate($value);
            $this->setValue($value);
        }
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
        $options = [
            'options' => [
                'min_range' => 1,
            ],
        ];

        if (! filter_var($id, FILTER_VALIDATE_INT, $options)) {
            throw new InvalidArgumentException(
                sprintf('<%s> does not allow the value <%s>.', static::class, $id)
            );
        }
    }
}
