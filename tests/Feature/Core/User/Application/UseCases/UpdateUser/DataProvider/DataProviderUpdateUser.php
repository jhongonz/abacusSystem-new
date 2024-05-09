<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Application\UseCases\UpdateUser\DataProvider;

final class DataProviderUpdateUser
{
    public static function provider(): array
    {
        $datetime = new \DateTime;

        return [
            [
                'dataUpdate' => [
                    'employeeId' => 2,
                    'profileId' => 2,
                    'login' => 'login-new',
                    'password' => 'prueba',
                    'state' => 2,
                    'createdAt' => $datetime,
                    'updatedAt' => $datetime,
                    'image' => 'photo.jpg',
                ],
            ],
        ];
    }
}
