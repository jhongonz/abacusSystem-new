<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 12:18:27
 */

namespace Core\Campus\Domain\ValueObjects;

class CampusAddress
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
