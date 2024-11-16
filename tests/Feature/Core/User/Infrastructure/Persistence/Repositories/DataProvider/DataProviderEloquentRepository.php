<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-04-28 18:52:33
 */

namespace Tests\Feature\Core\User\Infrastructure\Persistence\Repositories\DataProvider;

final class DataProviderEloquentRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function providerInsert(): array
    {
        $datetime = new \DateTime('2024-04-28 09:46:00');

        return [
            [
                'dataInsert' => [
                    'user_state' => 1,
                    'user_id' => null,
                    'user__emp_id' => 1,
                    'user__pro_id' => 1,
                    'user_login' => 'login',
                    'user_photo' => 'image.jpg',
                    'created_at' => '2024-04-28T09:46:00.000000Z',
                    'updated_at' => '2024-04-28T09:46:00.000000Z',
                ],
                'dateCreated' => $datetime,
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function providerUpdate(): array
    {
        $datetime = new \DateTime('2024-04-28 09:46:00');

        return [
            [
                'dataReturn' => [
                    'user_state' => 1,
                    'user_id' => 10,
                    'user__emp_id' => 5,
                    'user__pro_id' => 1,
                    'user_login' => 'prueba',
                    'user_photo' => 'other-testing.jpg',
                    'created_at' => '2024-04-28T09:46:00.000000Z',
                    'updated_at' => '2024-04-28T09:46:00.000000Z',
                ],
                'dataUpdate' => [
                    'user_state' => 1,
                    'user_id' => 10,
                    'user__emp_id' => 5,
                    'user__pro_id' => 1,
                    'user_login' => 'login',
                    'user_photo' => 'image.jpg',
                    'created_at' => '2024-04-28T09:46:00.000000Z',
                    'updated_at' => '2024-04-28T09:46:00.000000Z',
                ],
                'dateUpdated' => $datetime,
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function providerDelete(): array
    {
        return [
            [
                'dataReturn' => [
                    'user_state' => 1,
                    'user_id' => 7,
                    'user__emp_id' => 1,
                    'user__pro_id' => 1,
                    'user_login' => 'login',
                    'user_photo' => 'image.jpg',
                    'created_at' => '2024-04-28T09:46:00.000000Z',
                    'updated_at' => '2024-04-28T09:46:00.000000Z',
                ],
            ],
        ];
    }
}
