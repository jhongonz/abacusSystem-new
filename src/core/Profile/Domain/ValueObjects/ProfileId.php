<?php

namespace Core\Profile\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectContract;

class ProfileId implements ValueObjectContract
{
    private null|int $value;

    public function __construct(null|int $value = null)
    {
        $this->value = $value;
    }

    /**
     * @return null|int
     */
    public function value(): null|int
    {
        return $this->value;
    }

    /**
     * @param int $value
     * @return self
     */
    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }
}
