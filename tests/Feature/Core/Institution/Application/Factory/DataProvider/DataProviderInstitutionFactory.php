<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-20 21:31:00
 */

namespace Tests\Feature\Core\Institution\Application\Factory\DataProvider;

final class DataProviderInstitutionFactory
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function provider(): array
    {
        return [
            [
                'data' => [
                    'institution' => [
                        'id' => 1,
                        'name' => 'Testing',
                        'shortname' => 'Testing',
                        'code' => 'code',
                        'logo' => 'logo',
                        'search' => 'search',
                        'observations' => 'observations',
                        'state' => 1,
                        'address' => 'address',
                        'phone' => 'phone',
                        'email' => 'algo@algo.com',
                        'createdAt' => '2024-04-21 10:24:00',
                        'updatedAt' => '2024-04-21 10:24:00',
                    ]
                ]
            ]
        ];
    }
}
