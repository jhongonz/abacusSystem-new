<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-05 06:51:01
 */

namespace App\Traits;

use DateTime;
use Exception;

trait UtilsDateTimeTrait
{
    /**
     * @return DateTime
     */
    public function getCurrentTime(): DateTime
    {
        return new DateTime;
    }

    public function getCurrentTimeToArray(): array
    {
        return json_decode(json_encode(new DateTime), true);
    }

    /**
     * @throws Exception
     */
    public function getDateTime(string $datetime): DateTime
    {
        return new DateTime($datetime);
    }

    /**
     * @throws Exception
     */
    public function getDateTimeToArray(string $datetime): array
    {
        return json_decode(json_encode(new DateTime($datetime)), true);
    }
}
