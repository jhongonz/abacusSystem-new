<?php

namespace Core\Employee\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectContract;
use DateTime;

class EmployeeBirthdate implements ValueObjectContract
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

    /**
     * @param  null|DateTime  $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }
}
