<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Application\Factory\DataProvider;

final class DataProviderUserFactory
{
    /**
     * @return array<int, array<int, array<string, mixed>>>
     */
    public static function provider(): array
    {
        return [
            [
                [
                    'user' => [
                        'id' => 1,
                        'employeeId' => 1,
                        'profileId' => 1,
                        'login' => 'login',
                        'password' => '12345',
                        'state' => 2,
                        'createdAt' => '2024-04-21 10:24:00',
                        'updatedAt' => '2024-04-21 10:24:00',
                        'photo' => 'image.jpg',
                    ],
                ],
            ],
        ];
    }
}
