<?php

namespace Core\Profile\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectStatus;

class ModuleState extends ValueObjectStatus
{
    public function __construct(int $value = self::STATE_NEW)
    {
        parent::__construct($value);
    }
}