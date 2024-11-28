<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-24 22:57:57
 */

namespace Tests\Feature\Core\Campus\Application\DataProvider;

use Core\Campus\Domain\Campus;

final class CampusFactoryDataProvider
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function provider_dataArray(): array
    {
        return [
            [
                'dataTest' => [
                    Campus::TYPE => [
                        'id' => 1,
                        'institutionId' => 1,
                        'name' => 'name',
                        'address' => 'address',
                        'phone' => 'phone',
                        'email' => 'test@test.com',
                        'observations' => 'observations',
                        'state' => 1,
                        'createdAt' => '2024-06-24 11:02:00',
                        'updatedAt' => '2024-06-24 11:02:00',
                    ],
                ],
            ],
        ];
    }
}
