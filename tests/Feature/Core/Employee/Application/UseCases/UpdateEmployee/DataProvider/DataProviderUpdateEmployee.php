<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-04-30 11:03:35
 */

namespace Tests\Feature\Core\Employee\Application\UseCases\UpdateEmployee\DataProvider;

final class DataProviderUpdateEmployee
{
    public static function provider(): array
    {
        return [
            [
                'dataUpdate' => [
                    'identifier' => '12345',
                    'typeDocument' => '12435',
                    'name' => 'Jhon',
                    'lastname' => 'Smith',
                    'email' => 'test@test.com',
                    'phone' => '123456789',
                    'address' => 'Address',
                    'birthdate' => new \DateTime('2024-04-30 14:59:00'),
                    'observations' => 'Data for testing',
                    'state' => 2,
                    'image' => 'image.jpg',
                ],
            ],
        ];
    }
}
