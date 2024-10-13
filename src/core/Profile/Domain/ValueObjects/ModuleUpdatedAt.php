<?php

namespace Core\Profile\Domain\ValueObjects;

use DateTime;

class ModuleUpdatedAt
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

    public function setValue(DateTime $value): self
    {
        $this->value = $value;

        return $this;
    }
}
