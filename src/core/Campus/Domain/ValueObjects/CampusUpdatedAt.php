<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 12:22:59
 */

namespace Core\Campus\Domain\ValueObjects;

use Core\SharedContext\Model\dateTimeModel;

class CampusUpdatedAt implements dateTimeModel
{
    public function __construct(
        private ?\DateTime $value = null,
    ) {
    }

    public function value(): ?\DateTime
    {
        return $this->value;
    }

    public function setValue(\DateTime $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function toFormattedString(): string
    {
        return (!is_null($this->value)) ? $this->value->format(self::DATE_FORMAT) : '';
    }

    public function __toString(): string
    {
        return $this->toFormattedString();
    }
}
