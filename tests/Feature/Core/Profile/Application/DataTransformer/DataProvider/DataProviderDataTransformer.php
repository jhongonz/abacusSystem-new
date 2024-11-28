<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-06 20:14:00
 */

namespace Tests\Feature\Core\Profile\Application\DataTransformer\DataProvider;

final class DataProviderDataTransformer
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function providerModuleToReadToShare(): array
    {
        $datetime = '2024-05-06 20:12:00';

        return [
            [
                'expected' => [
                    'id' => 1,
                    'key' => 'test',
                    'name' => 'test',
                    'route' => 'test',
                    'icon' => 'test',
                    'state' => 1,
                    'position' => 2,
                    'createdAt' => '2024-05-06 20:12:00',
                    'updatedAt' => $datetime,
                    'state_literal' => 'test',
                ],
                'datetime' => $datetime,
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function providerModuleToRead(): array
    {
        $datetime = '2024-05-06 20:12:00';

        return [
            [
                'expected' => [
                    'module' => [
                        'id' => 1,
                        'key' => 'test',
                        'name' => 'test',
                        'route' => 'test',
                        'icon' => 'test',
                        'state' => 1,
                        'position' => 2,
                        'createdAt' => $datetime,
                        'updatedAt' => $datetime,
                    ],
                ],
                'datetime' => $datetime,
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function providerProfileToRead(): array
    {
        $datetime = '2024-05-06 20:12:00';

        return [
            [
                'expected' => [
                    'profile' => [
                        'id' => 1,
                        'name' => 'test',
                        'description' => 'test',
                        'state' => 1,
                        'createdAt' => $datetime,
                        'updatedAt' => $datetime,
                        'modulesAggregator' => [],
                    ],
                ],
                'datetime' => $datetime,
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function providerProfileToReadToShare(): array
    {
        $datetime = '2024-05-06 20:12:00';

        return [
            [
                'expected' => [
                    'id' => 1,
                    'name' => 'test',
                    'description' => 'test',
                    'state' => 1,
                    'createdAt' => $datetime,
                    'updatedAt' => $datetime,
                    'modulesAggregator' => [],
                    'state_literal' => 'test',
                ],
                'datetime' => $datetime,
            ],
        ];
    }
}
