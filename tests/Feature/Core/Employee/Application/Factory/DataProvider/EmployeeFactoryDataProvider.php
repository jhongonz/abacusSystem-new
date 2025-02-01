<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-04-29 17:57:23
 */

namespace Tests\Feature\Core\Employee\Application\Factory\DataProvider;

final class EmployeeFactoryDataProvider
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function provider(): array
    {
        return [
            [
                'dataObject' => [
                    'employee' => [
                        'id' => 1,
                        'institutionId' => 1,
                        'identification' => '123456',
                        'name' => 'peter',
                        'lastname' => 'smith',
                        'state' => 1,
                        'identification_type' => 'type',
                        'userId' => 1,
                        'address' => 'address',
                        'phone' => '12345',
                        'email' => 'some@some.com',
                        'observations' => 'testing',
                        'image' => 'photo.jpg',
                        'search' => 'testing object employee',
                        'birthdate' => '2024-04-29 19:40:01',
                        'createdAt' => '2024-04-29 19:40:01',
                        'updatedAt' => '2024-04-29 19:40:01',
                    ],
                ],
            ],
        ];
    }
}
