<?php

namespace Core\User\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectStatus;

class UserState extends ValueObjectStatus
{
    public function __construct(int $value = self::STATE_DEFAULT)
    {
        parent::__construct($value);
    }
}