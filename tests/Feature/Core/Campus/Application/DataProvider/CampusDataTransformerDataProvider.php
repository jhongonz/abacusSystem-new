<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-24 08:41:02
 */

namespace Tests\Feature\Core\Campus\Application\DataProvider;

final class CampusDataTransformerDataProvider
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function provider_read(): array
    {
        return [
            [
                'dataTest' => self::retrieveGenericDataTest(),
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function provider_readToShare(): array
    {
        $dataExpected = self::retrieveGenericDataTest();
        $dataExpected['state_literal'] = '<html lang="es"></html>';

        return [
            [
                'dataExpected' => $dataExpected,
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
            'institutionId' => 1,
            'name' => 'name',
            'address' => 'address',
            'phone' => 'phone',
            'email' => 'test@test.com',
            'observations' => 'observations',
            'search' => 'search',
            'state' => 1,
            'createdAt' => '2024-06-24 08:23:00',
            'updatedAt' => '2024-06-24 08:23:00',
        ];
    }
}
