<?php

namespace Core\Profile\Domain\ValueObjects;

class ModulePosition
{
    private int $value;

    public function __construct(?int $value = 1)
    {
        $this->value = $value;
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
