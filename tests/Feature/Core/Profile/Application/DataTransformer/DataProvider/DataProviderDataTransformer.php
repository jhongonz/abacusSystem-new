<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-06 20:14:00
 */

namespace Tests\Feature\Core\Profile\Application\DataTransformer\DataProvider;

final class DataProviderDataTransformer
{
    public static function providerModuleToReadToShare(): array
    {
        $datetime = new \DateTime('2024-05-06 20:12:00');

        return [
            [
                'expected' => [
                    'id' => 1,
                    'key' => 'test',
                    'name' => 'test',
                    'route' => 'test',
                    'icon' => 'test',
                    'state' => 1,
                    'createdAt' => $datetime,
                    'updatedAt' => $datetime,
                    'state_literal' => 'test',
                ],
                'datetime' => $datetime,
            ],
        ];
    }

    public static function providerModuleToRead(): array
    {
        $datetime = new \DateTime('2024-05-06 20:12:00');

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
                        'createdAt' => $datetime,
                        'updatedAt' => $datetime,
                    ],
                ],
                'datetime' => $datetime,
            ],
        ];
    }

    public static function providerProfileToRead(): array
    {
        $datetime = new \DateTime('2024-05-06 20:12:00');

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

    public static function providerProfileToReadToShare(): array
    {
        $datetime = new \DateTime('2024-05-06 20:12:00');

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
