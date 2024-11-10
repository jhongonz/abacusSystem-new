<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Domain\ValueObjects;

use Core\SharedContext\Model\dateTimeModel;
use DateTime;

class UserCreatedAt implements dateTimeModel
{
    public function __construct(
        private DateTime $value = new DateTime
    ) {
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

    public function __toString(): string
    {
        return $this->toFormattedString();
    }

    public function toFormattedString(): string
    {
        return $this->value->format(self::DATE_FORMAT);
    }
}
