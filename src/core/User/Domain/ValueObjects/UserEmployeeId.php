<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectContract;
use InvalidArgumentException;

class UserEmployeeId implements ValueObjectContract
{
    private ?int $value;

    public function __construct(?int $value = null)
    {
        if (! is_null($value)) {
            $this->validate($value);
        }
        $this->value = $value;
    }

    public function value(): ?int
    {
        return $this->value;
    }

    /**
     * @param  int  $value
     * @return $this
     */
    public function setValue($value): self
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
