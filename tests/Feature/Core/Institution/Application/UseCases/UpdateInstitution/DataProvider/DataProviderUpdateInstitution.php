<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-22 13:44:10
 */

namespace Tests\Feature\Core\Institution\Application\UseCases\UpdateInstitution\DataProvider;

final class DataProviderUpdateInstitution
{
    public static function provider(): array
    {
        return [
            [
                'data' => [
                    'code' => 'code',
                    'name' => 'name',
                    'shortname' => 'shortname',
                    'logo' => 'logo',
                    'observations' => 'observations',
                    'state' => 2
                ]
            ]
        ];
    }
}
