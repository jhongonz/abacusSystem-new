<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-19 14:02:32
 */

namespace Core\Institution\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectContract;
use DateTime;

class InstitutionUpdatedAt implements ValueObjectContract
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

    /**
     * @param DateTime $value
     * @return self
     */
    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }
}
