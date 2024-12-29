<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-28 13:24:45
 */

namespace Tests\Feature\Core\Employee\Application\DataProvider;

final class EmployeeDataTransformerDataProvider
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function provider_read(): array
    {
        return [
            [
                'dataTest' => self::retrieveGenericDataTest(),
            ],
        ];
    }

    public static function provider_readToShare(): array
    {
        $dataExpected = self::retrieveGenericDataTest();
        $dataExpected['html'] = '<span class="badge badge-success">Activo</span>';

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
            'userId' => 1,
            'institutionId' => 1,
            'identification' => '123456789',
            'identification_type' => 'PASSPORT',
            'name' => 'Pedro',
            'lastname' => 'Perez',
            'phone' => '662498025',
            'email' => 'algo@algo.com',
            'address' => 'Av Testing',
            'birthdate' => '10-10-1985 00:00:00',
            'observations' => 'Testing data provider',
            'image' => 'testing.jpg',
            'search' => 'search text',
            'state' => 2,
            'createdAt' => '2024-12-28 13:37:00',
            'updatedAt' => '2024-12-28 13:37:00',
        ];
    }
}
