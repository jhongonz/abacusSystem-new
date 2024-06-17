<?php

namespace Core\Employee\Domain\ValueObjects;

use DateTime;

class EmployeeUpdateAt
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

    public function setValue(Datetime $value): self
    {
        $this->value = $value;

        return $this;
    }
}
