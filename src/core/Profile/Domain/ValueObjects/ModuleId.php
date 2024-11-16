<?php

namespace Core\Profile\Domain\ValueObjects;

class ModuleId
{
    public function __construct(
        private ?int $value = null,
    ) {
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
