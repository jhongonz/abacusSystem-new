<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-31 18:07:12
 */

namespace Tests\Feature\Core\Institution\Application\DataProvider;

final class InstitutionDataTransformerDataProvider
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function provider_read(): array
    {
        $dataWithNull = self::retrieveGenericDataTest();
        $dataWithNull['updatedAt'] = '';

        return [
            [
                'dataTestWithNull' => $dataWithNull,
            ],
            [
                'dataTest' => self::retrieveGenericDataTest(),
            ],
        ];
    }

    public static function provider_readToShare(): array
    {
        $dataExpected = self::retrieveGenericDataTest();
        $dataExpected['state_literal'] = '<span class="badge badge-success">Activo</span>';

        return [
            [
                'dataTest' => $dataExpected,
            ],
        ];
    }

    /**
     * @return array<string, int|string>
     */
    private static function retrieveGenericDataTest(): array
    {
        return [
            'id' => 1,
            'code' => 'COLE',
            'name' => 'Colegio',
            'shortname' => 'COLE',
            'logo' => 'image.jpg',
            'observations' => 'observations',
            'address' => 'address',
            'phone' => '123456789',
            'email' => 'test@test.com',
            'state' => 2,
            'search' => 'testing',
            'createdAt' => '2024-12-31 18:13:00',
            'updatedAt' => '2024-12-31 18:13:00',
        ];
    }
}
