<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectContract;

class UserProfileId implements ValueObjectContract
{
    private ?int $value;

    public function __construct(?int $id = null)
    {
        $this->value = $id;
    }

    public function value(): ?int
    {
        return $this->value;
    }

    /**
     * @param  int  $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }
}
