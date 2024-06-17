<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Domain\ValueObjects;

use InvalidArgumentException;

class UserProfileId
{
    private ?int $value;

    public function __construct(?int $id = null)
    {
        if(! is_null($id)) {
            $this->validate($id);
        }

        $this->value = $id;
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
        $options = [
            'options' => [
                'min_range' => 1,
            ],
        ];

        if (! filter_var($value, FILTER_VALIDATE_INT, $options)) {
            throw new InvalidArgumentException(
                sprintf('<%s> does not allow the value <%s>.', static::class, $value)
            );
        }
    }
}
