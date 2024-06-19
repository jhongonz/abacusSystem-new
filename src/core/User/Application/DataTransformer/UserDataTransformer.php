<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Application\DataTransformer;

use Core\User\Domain\Contracts\UserDataTransformerContract;
use Core\User\Domain\User;

class UserDataTransformer implements UserDataTransformerContract
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';
    private User $user;

    public function write(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function read(): array
    {
        $data = [
            User::TYPE => [
                'id' => $this->user->id()->value(),
                'employeeId' => $this->user->employeeId()->value(),
                'profileId' => $this->user->profileId()->value(),
                'login' => $this->user->login()->value(),
                'password' => $this->user->password()->value(),
                'state' => $this->user->state()->value(),
                'photo' => $this->user->photo()->value(),
                'createdAt' => $this->user->createdAt()->value()->format(self::DATE_FORMAT),
            ],
        ];

        $updatedAt = $this->user->updatedAt()->value();
        $data['updatedAt'] = (! is_null($updatedAt)) ? $updatedAt->format(self::DATE_FORMAT) : null;

        return $data;
    }
}
