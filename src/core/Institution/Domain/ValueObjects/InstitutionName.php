<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-19 13:48:59
 */

namespace Core\Institution\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectContract;

class InstitutionName implements ValueObjectContract
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
    public function value(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }
}
