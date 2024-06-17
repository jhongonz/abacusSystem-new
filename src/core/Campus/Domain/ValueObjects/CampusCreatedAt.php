<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 12:22:59
 */

namespace Core\Campus\Domain\ValueObjects;

use DateTime;

class CampusCreatedAt
{
    private DateTime $value;

    public function __construct(DateTime $value = new DateTime)
    {
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
