<?php

namespace Core\Employee\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectContract;

class EmployeeObservations implements ValueObjectContract
{
    private ?string $value;

    public function __construct(?string $value = null)
    {
        $this->value = $value;
    }

    public function value(): ?string
    {
        return $this->value;
    }

    /**
     * @param  null|string $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }
}
