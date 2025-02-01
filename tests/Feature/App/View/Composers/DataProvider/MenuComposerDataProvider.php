<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-23 18:34:57
 */

namespace Tests\Feature\App\View\Composers\DataProvider;

final class MenuComposerDataProvider
{
    public static function provider_menuOptionsParent(): array
    {
        return [
            [
                'optionsExpected' => [
                    [
                        'id' => null,
                        'key' => 'managers',
                        'name' => 'Gestión Administrativa',
                        'icon' => 'fas fa-tools',
                        'route' => null,
                    ],
                    [
                        'id' => null,
                        'key' => 'setting',
                        'name' => 'Testing',
                        'icon' => 'fas fa-tools',
                        'route' => 'testing',
                    ],
                ],
            ],
        ];
    }
}
