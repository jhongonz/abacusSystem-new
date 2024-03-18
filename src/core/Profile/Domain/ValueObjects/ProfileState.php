<?php

namespace Core\Profile\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectStatus;

class ProfileState extends ValueObjectStatus
{
    public function __construct(int $value = self::STATE_NEW)
    {
        parent::__construct($value);
    }
}