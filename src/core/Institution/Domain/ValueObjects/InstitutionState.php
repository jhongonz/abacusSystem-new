<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-19 14:01:07
 */

namespace Core\Institution\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectStatus;

class InstitutionState extends ValueObjectStatus
{
    public function __construct(int $value = self::STATE_NEW)
    {
        parent::__construct($value);
    }
}
