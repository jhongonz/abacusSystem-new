<?php

namespace Core\Profile\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectContract;

class ModuleId implements ValueObjectContract
{
    private null|int $value;
    
    public function __construct(null|int $value = null)
    {
        $this->value = $value;
    }

    public function value(): null|int
    {
        return $this->value;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }
}