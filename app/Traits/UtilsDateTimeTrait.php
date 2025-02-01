<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 06:51:01
 */

namespace App\Traits;

trait UtilsDateTimeTrait
{
    private function getCurrentTime(): \DateTime
    {
        return new \DateTime();
    }

    /**
     * @throws \Exception
     */
    private function getDateTime(string $datetime): \DateTime
    {
        return new \DateTime($datetime);
    }
}
