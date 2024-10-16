<?php

namespace Core\Profile\Domain\ValueObjects;

use DateTime;

class ProfileUpdatedAt
{
    public function __construct(
        private ?DateTime $value = null
    ) {
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
