<?php

namespace Core\Profile\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectContract;
use DateTime;

class ProfileUpdatedAt implements ValueObjectContract
{
    private ?DateTime $value;

    public function __construct(?DateTime $value = null)
    {
        $this->value = $value;
    }

    public function value(): ?DateTime
    {
        return $this->value;
    }

    /**
     * @param  DateTime  $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }
}
