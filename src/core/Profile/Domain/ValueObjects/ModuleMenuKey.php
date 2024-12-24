<?php

namespace Core\Profile\Domain\ValueObjects;

class ModuleMenuKey
{
    public function __construct(
        private string $value,
    ) {
    }

    public function value(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
