<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-15 20:58:14
 */

namespace Core\Institution\Domain\ValueObjectsContactCard;

use DateTime;

class ContactCreatedAt
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

    public function setValue(DateTime $dateTime): self
    {
        $this->value = $dateTime;
        return $this;
    }
}
