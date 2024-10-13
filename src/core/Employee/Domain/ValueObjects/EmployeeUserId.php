<?php

namespace Core\Employee\Domain\ValueObjects;

class EmployeeUserId
{
    private ?int $value;

    public function __construct(?int $value = null)
    {
        $this->value = $value;
    }

    public function value(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }
}
