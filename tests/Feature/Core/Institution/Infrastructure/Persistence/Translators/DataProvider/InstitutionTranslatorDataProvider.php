<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2025-01-03 16:39:12
 */

namespace Tests\Feature\Core\Institution\Infrastructure\Persistence\Translators\DataProvider;

final class InstitutionTranslatorDataProvider
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function provider(): array
    {
        $datetime = new \DateTime();

        return [
            [
                [
                    'id' => 1,
                    'name' => 'Test Institution',
                    'shortname' => 'Test Institution',
                    'code' => 'code test',
                    'logo' => 'logo',
                    'state' => 1,
                    'observations' => 'observations',
                    'address' => 'address',
                    'phone' => '123456789',
                    'email' => 'test@test.com',
                    'search' => 'testing testing',
                    'createdAt' => $datetime,
                    'updatedAt' => $datetime,
                ],
            ],
            [
                [
                    'id' => 2,
                    'name' => 'Test Institution',
                    'shortname' => 'Test Institution',
                    'code' => 'code test',
                    'logo' => 'logo',
                    'state' => 2,
                    'observations' => 'observations',
                    'address' => 'address',
                    'phone' => '987654321',
                    'email' => 'test@test.com',
                    'search' => 'testing testing',
                    'createdAt' => null,
                    'updatedAt' => null,
                ],
            ],
        ];
    }
}
