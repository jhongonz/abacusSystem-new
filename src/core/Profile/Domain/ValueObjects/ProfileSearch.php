<?php

namespace Core\Profile\Domain\ValueObjects;

class ProfileSearch
{
    public function __construct(
        private ?string $value = null
    ) {
    }

    public function value(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
