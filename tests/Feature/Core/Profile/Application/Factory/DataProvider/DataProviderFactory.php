<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-06 21:35:19
 */

namespace Tests\Feature\Core\Profile\Application\Factory\DataProvider;

use DateTime;

final class DataProviderFactory
{
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
                        'createdAt' => new DateTime('2024-05-06 21:42:01'),
                        'updatedAt' => new DateTime('2024-05-06 21:42:01'),
                    ],
                ],
            ],
        ];
    }

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
                                'createdAt' => new DateTime('2024-05-06 21:42:01'),
                                'updatedAt' => new DateTime('2024-05-06 21:42:01'),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

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
                        'createdAt' => json_decode(json_encode(new DateTime('2024-05-06 21:42:01')), true),
                        'updatedAt' => json_decode(json_encode(new DateTime('2024-05-06 21:42:01')), true),
                    ],
                ],
            ],
        ];
    }
}
