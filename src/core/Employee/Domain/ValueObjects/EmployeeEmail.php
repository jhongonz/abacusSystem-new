<?php

namespace Core\Employee\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectContract;
use InvalidArgumentException;

class EmployeeEmail implements ValueObjectContract
{
    private ?string $value;

    public function __construct(?string $value = null)
    {
        if (! is_null($value)) {
            $this->validate($value);
        }
        $this->value = $value;
    }

    public function value(): ?string
    {
        return $this->value;
    }

    /**
     * @param  string  $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->validate($value);
        $this->value = $value;

        return $this;
    }

    private function validate(string $value): void
    {
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(
                sprintf('<%s> does not allow the invalid email: <%s>.', static::class, $value)
            );
        }
    }
}
