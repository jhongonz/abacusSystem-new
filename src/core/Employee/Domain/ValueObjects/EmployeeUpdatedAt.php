<?php

namespace Core\Employee\Domain\ValueObjects;

use Core\SharedContext\Model\dateTimeModel;
use DateTime;

class EmployeeUpdatedAt implements dateTimeModel
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

    public function __toString(): string
    {
        return $this->toFormattedString();
    }

    public function toFormattedString(): string
    {
        return (! is_null($this->value)) ? $this->value->format(self::DATE_FORMAT) : '';
    }
}
