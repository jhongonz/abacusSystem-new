<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Application\DataTransformer\DataProvider;

final class DataProviderDataTransformer
{
    public static function provider(): array
    {
        $datetime = '2024-04-20 22:08:42';

        return [
            [
                'expected' => [
                    'user' => [
                        'id' => 1,
                        'employeeId' => 1,
                        'profileId' => 1,
                        'login' => 'login',
                        'password' => '12345',
                        'state' => 1,
                        'photo' => 'test.jpg',
                        'createdAt' => $datetime,
                        'updatedAt' => $datetime,
                    ],
                ],
                'datetime' => $datetime,
            ],
        ];
    }
}
