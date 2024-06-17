<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 12:22:59
 */

namespace Core\Campus\Domain\ValueObjects;

use DateTime;

class CampusUpdatedAt
{
    private ?DateTime $value;

    public function __construct(DateTime $value = null)
    {
        $this->value = $value;
    }

    public function value(): ?DateTime
    {
        return $this->value;
    }

    public function setValue(DateTime $value): self
    {
        $this->value = $value;
        return $this;
    }
}
