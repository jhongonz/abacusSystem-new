<?php

namespace Core\Employee\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectContract;

class EmployeeIdentification implements ValueObjectContract
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
     * @param  string  $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }
}
