<?php

namespace Core\Employee\Domain\ValueObjects;

use DateTime;

class EmployeeBirthdate
{
    public function __construct(
        private ?DateTime $value = null
    ) {
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
