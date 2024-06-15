<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-15 20:57:24
 */

namespace Core\Institution\Domain\ValueObjectsContactCard;

use Core\SharedContext\Model\ValueObjectStatus;

class ContactState extends ValueObjectStatus
{
    public function __construct(int $value = self::STATE_NEW)
    {
        parent::__construct($value);
    }
}
