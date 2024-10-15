<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-16 19:26:00
 */

namespace Core\Institution\Domain\ValueObjects;

class InstitutionAddress
{
    public function __construct(
        private ?string $value = null
    ) {
    }

    public function value(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }
}
