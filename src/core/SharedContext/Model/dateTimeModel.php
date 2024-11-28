<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-10-19 21:33:33
 */

namespace Core\SharedContext\Model;

interface dateTimeModel
{
    public const DATE_FORMAT = 'Y-m-d H:i:s';

    public function __toString(): string;

    public function toFormattedString(): string;
}
