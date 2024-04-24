<?php

namespace Core\Employee\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectContract;
use DateTime;

class EmployeeBirthdate implements ValueObjectContract
{
    private null|DateTime $value;

    public function __construct(null|DateTime $value = null)
    {
        $this->value = $value;
    }

    public function value(): null|DateTime
    {
        return $this->value;
    }

    public function toString(): null|string
    {
        return ($this->value) ? $this->value->format('d/m/Y') : null;
    }

    /**
     * @param DateTime $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }
}
