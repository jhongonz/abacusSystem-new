<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-15 20:47:45
 */

namespace Core\Institution\Domain\ValueObjectsContactCard;

use Core\SharedContext\Model\ValueObjectContract;

class ContactDefault
{
    private bool $value;

    public function __construct(bool $value = false)
    {
        $this->value = $value;
    }

    public function value(): bool
    {
        return $this->value;
    }

    public function setValue(bool $value): self
    {
        $this->value = $value;
        return $this;
    }
}
