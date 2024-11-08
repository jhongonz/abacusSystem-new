<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-19 14:02:32
 */

namespace Core\Institution\Domain\ValueObjects;

use Core\SharedContext\Model\dateTimeModel;
use DateTime;

class InstitutionCreatedAt implements dateTimeModel
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
        return (! is_null($this->value)) ? $this->value->format(self::DATE_FORMAT) : '';
    }
}
