<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-17 12:21:52
 */

namespace Core\Campus\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectStatus;

class CampusState extends ValueObjectStatus
{
    public function __construct(int $value = self::STATE_NEW)
    {
        parent::__construct($value);
    }
}
