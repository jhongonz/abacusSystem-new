<?php

namespace Core\Employee\Domain\ValueObjects;

class EmployeeIdentification
{
    public function __construct(
        private ?string $value = null
    ) {
    }

    public function value(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
