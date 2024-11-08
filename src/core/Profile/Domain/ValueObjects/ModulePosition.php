<?php

namespace Core\Profile\Domain\ValueObjects;

class ModulePosition
{
    public function __construct(
        private int $value = 1
    ) {
    }

    public function value(): int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }
}
