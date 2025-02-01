<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2025-01-06 18:36:21
 */

namespace Tests\Feature\Core\Profile\Infrastructure\Persistence\Repositories\DataProvider;

final class EloquentModuleRepositoryDataProvider
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function providerCreateModule(): array
    {
        $datetime = new \DateTime();

        return [
            [
                [
                    'id' => null,
                    'name' => 'test',
                    'menuKey' => 'testing',
                    'route' => 'testing',
                    'icon' => 'image.jpg',
                    'state' => 1,
                    'search' => 'test test',
                    'position' => 4,
                    'createdAt' => $datetime,
                    'updatedAt' => $datetime,
                ],
            ],
        ];
    }
}
