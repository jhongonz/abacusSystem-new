<?php

namespace Core\Profile\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectContract;

class ModuleId implements ValueObjectContract
{
    private ?int $value;

    public function __construct(?int $value = null)
    {
        $this->value = $value;
    }

    public function value(): ?int
    {
        return $this->value;
    }

    /**
     * @param  int  $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }
}
