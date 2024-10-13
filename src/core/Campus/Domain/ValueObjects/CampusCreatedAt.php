<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 12:22:59
 */

namespace Core\Campus\Domain\ValueObjects;

use DateTime;

class CampusCreatedAt
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

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

    public function toFormattedString(): string
    {
        return $this->value->format(self::DATE_FORMAT);
    }

    public function __toString(): string
    {
        return $this->toFormattedString();
    }
}
