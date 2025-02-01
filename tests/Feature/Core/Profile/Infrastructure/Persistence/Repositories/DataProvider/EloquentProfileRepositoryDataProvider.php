<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2025-01-06 18:36:21
 */

namespace Tests\Feature\Core\Profile\Infrastructure\Persistence\Repositories\DataProvider;

final class EloquentProfileRepositoryDataProvider
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function providerCreateProfile(): array
    {
        $datetime = new \DateTime();

        return [
            [
                [
                    'name' => 'test',
                    'state' => 1,
                    'search' => 'test test',
                    'description' => 'This is a example test',
                    'createdAt' => $datetime,
                    'updatedAt' => $datetime,
                ],
            ],
        ];
    }
}
