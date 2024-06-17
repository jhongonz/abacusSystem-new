<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-19 13:48:59
 */

namespace Core\Institution\Domain\ValueObjects;

class InstitutionLogo
{
    private ?string $value;

    public function __construct(?string $value = null)
    {
        $this->value = $value;
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
