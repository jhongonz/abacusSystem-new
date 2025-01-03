<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-29 17:32:29
 */

namespace Tests\Feature\Core\Employee\Infrastructure\Persistence\Translators\DataProvider;

use Core\User\Infrastructure\Persistence\Eloquent\Model\User;

final class EmployeeTranslatorDataProvider
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function provider(): array
    {
        return [
            [
                null,
            ],
            [
                User::class,
            ],
        ];
    }
}
