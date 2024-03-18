<?php

namespace Core\Employee\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectStatus;

class EmployeeState extends ValueObjectStatus
{
    public function __construct(int $value = self::STATE_NEW)
    {
        parent::__construct($value);
    }
}