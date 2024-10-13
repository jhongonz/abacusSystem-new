<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Domain\ValueObjects;

use DateTime;

class UserCreatedAt
{
    private DateTime $value;

    public function __construct(
        DateTime $value = new DateTime
    ) {
        $this->value = $value;
    }

    public function value(): DateTime
    {
        return $this->value;
    }

    public function setValue(DateTime $value): self
    {
        $this->value = $value;

        return $this;
    }
}
