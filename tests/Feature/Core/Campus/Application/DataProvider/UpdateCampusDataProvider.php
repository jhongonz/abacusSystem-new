<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-25 22:11:40
 */

namespace Tests\Feature\Core\Campus\Application\DataProvider;

final class UpdateCampusDataProvider
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function provider_update_campus(): array
    {
        return [
            [
                'dataTest' => [
                    'institutionId' => 1,
                    'name' => 'name',
                    'address' => 'address',
                    'phone' => 'phone',
                    'email' => 'email',
                    'observations' => 'observations',
                    'search' => 'search',
                    'state' => 1,
                    'createdAt' => new \DateTime('2024-12-25 18:28:00'),
                    'updatedAt' => new \DateTime('2024-12-25 18:28:00'),
                ],
            ],
        ];
    }
}
