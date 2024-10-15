<?php

namespace Core\Employee\Domain\ValueObjects;

use DateTime;

class EmployeeCreatedAt
{
    public function __construct(
        private DateTime $value = new DateTime
    ) {
    }

    public function value(): DateTime
    {
        return $this->value;
    }

    public function setValue(DateTime $value): self
    {
        $this->value = $value;

        return $this;
    }
}
