<?php

namespace Core\Profile\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectContract;
use DateTime;

class ProfileCreatedAt implements ValueObjectContract
{
    private DateTime $value;

    public function __construct(DateTime $value = new DateTime())
    {
        $this->value = $value;
    }

    public function value(): DateTime
    {
        return $this->value;
    }

    /**
     * @param DateTime $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }
}
