<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-22 12:47:14
 */
namespace Core\Profile\Domain\ValueObjects;

class MenuName
{
    public function __construct(
        private string $value
    ){}

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
