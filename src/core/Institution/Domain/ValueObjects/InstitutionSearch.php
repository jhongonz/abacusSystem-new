<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-19 14:11:29
 */

namespace Core\Institution\Domain\ValueObjects;

class InstitutionSearch
{
    public function __construct(
        private ?string $value = null,
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
