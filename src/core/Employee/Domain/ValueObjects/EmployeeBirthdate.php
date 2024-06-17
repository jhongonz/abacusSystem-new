<?php

namespace Core\Employee\Domain\ValueObjects;

use DateTime;

class EmployeeBirthdate
{
    private ?DateTime $value;

    public function __construct(?DateTime $value = null)
    {
        $this->value = $value;
    }

    public function value(): ?DateTime
    {
        return $this->value;
    }

    public function toString(): ?string
    {
        return ($this->value) ? $this->value->format('d/m/Y') : null;
    }

    public function setValue(?DateTime $value): self
    {
        $this->value = $value;

        return $this;
    }
}
