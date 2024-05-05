<?php

namespace Core\Profile\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectContract;
use DateTime;

class ModuleUpdatedAt implements ValueObjectContract
{
    private null|DateTime $value;

    public function __construct(null|DateTime $value = null)
    {
        $this->value = $value;
    }

    public function value(): null|DateTime
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
