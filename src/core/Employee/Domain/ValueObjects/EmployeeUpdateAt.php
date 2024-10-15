<?php

namespace Core\Employee\Domain\ValueObjects;

use DateTime;

class EmployeeUpdateAt
{
    public function __construct(
        private ?DateTime $value = null
    ) {
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
