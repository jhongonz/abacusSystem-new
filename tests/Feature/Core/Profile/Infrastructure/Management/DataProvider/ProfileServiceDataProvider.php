<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2025-01-04 17:59:15
 */

namespace Tests\Feature\Core\Profile\Infrastructure\Management\DataProvider;

final class ProfileServiceDataProvider
{
    /**
     * @return array<int, array<string, bool>>
     */
    public static function activated(): array
    {
        return [
            [
                true,
            ],
            [
                false,
            ],
        ];
    }
}
