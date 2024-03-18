<?php

namespace Core\Profile\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectContract;

class ModuleMenuKey implements ValueObjectContract
{
    private null|string $value;

    public function __construct(null|string $value = null)
    {
        $this->value = $value;
    }

    public function value(): null|string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }
}