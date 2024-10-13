<?php

namespace Core\Profile\Domain\ValueObjects;

use DateTime;

class ModuleCreatedAt
{
    private DateTime $value;

    public function __construct(
        DateTime $value = new DateTime
    ) {
        $this->value = $value;
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
