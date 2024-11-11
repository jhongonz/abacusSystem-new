<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-06 21:35:19
 */

namespace Tests\Feature\Core\Profile\Application\Factory\DataProvider;

final class DataProviderFactory
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function providerModule(): array
    {
        return [
            [
                'dataObject' => [
                    'module' => [
                        'id' => 1,
                        'key' => 'test',
                        'name' => 'test',
                        'route' => 'test',
                        'icon' => 'test',
                        'state' => 1,
                        'position' => 2,
                        'createdAt' => '2024-05-06 21:42:01',
                        'updatedAt' => '2024-05-06 21:42:01',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function providerModules(): array
    {
        return [
            [
                'dataObject' => [
                    'modules' => [
                        [
                            'module' => [
                                'id' => 1,
                                'key' => 'test',
                                'name' => 'test',
                                'route' => 'test',
                                'icon' => 'test',
                                'state' => 1,
                                'position' => 2,
                                'createdAt' => '2024-05-06 21:42:01',
                                'updatedAt' => '2024-05-06 21:42:01',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function providerProfile(): array
    {
        return [
            [
                'dataObject' => [
                    'profile' => [
                        'id' => 1,
                        'name' => 'test',
                        'state' => 1,
                        'description' => 'test',
                        'modulesAggregator' => [1, 2, 3],
                        'createdAt' => '2024-05-06 21:42:01',
                        'updatedAt' => '2024-05-06 21:42:01',
                    ],
                ],
            ],
        ];
    }
}
