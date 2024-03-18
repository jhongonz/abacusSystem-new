<?php

namespace Core\User\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectContract;

class UserProfileId implements ValueObjectContract
{
    private null|int $value;
    
    public function __construct(null|int $id = null)
    {
        $this->value = $id;
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