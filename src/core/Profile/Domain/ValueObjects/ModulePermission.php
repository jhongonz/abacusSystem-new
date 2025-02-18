<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2025-02-15 18:31:58
 */

namespace Core\Profile\Domain\ValueObjects;

class ModulePermission
{
    public function __construct(
        private string $value = 'read',
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
