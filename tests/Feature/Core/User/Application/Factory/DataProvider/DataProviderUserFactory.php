<?php

namespace Tests\Feature\Core\User\Application\Factory\DataProvider;

use DateTime;

final class DataProviderUserFactory
{
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
                        'createdAt' => json_decode(json_encode(new DateTime('2024-04-21 10:24:00')), true),
                        'photo' => 'image.jpg'
                    ]
                ],
            ]
        ];
    }
}
