<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-19 14:02:32
 */

namespace Core\Institution\Domain\ValueObjects;

use DateTime;

class InstitutionCreatedAt
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
