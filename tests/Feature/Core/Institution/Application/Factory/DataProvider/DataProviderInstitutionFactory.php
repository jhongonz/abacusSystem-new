<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-20 21:31:00
 */

namespace Tests\Feature\Core\Institution\Application\Factory\DataProvider;

use DateTime;

final class DataProviderInstitutionFactory
{
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
                        'email' => 'email',
                        'createdAt' => json_decode(json_encode(new DateTime), true),
                        'updatedAt' => json_decode(json_encode(new DateTime), true),
                    ]
                ]
            ]
        ];
    }
}
