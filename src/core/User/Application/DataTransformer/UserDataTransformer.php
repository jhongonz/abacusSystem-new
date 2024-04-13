<?php

namespace Core\User\Application\DataTransformer;

use Core\User\Domain\Contracts\UserDataTransformerContract;
use Core\User\Domain\User;

class UserDataTransformer implements UserDataTransformerContract
{
    private User $user;

    public function write(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function read(): array
    {
        return [
        User::TYPE => [
                'id' => $this->user->id()->value(),
                'employeeId' => $this->user->employeeId()->value(),
                'profileId' => $this->user->profileId()->value(),
                'login' => $this->user->login()->value(),
                'password' => $this->user->password()->value(),
                'state' => $this->user->state()->value(),
                'photo' => $this->user->photo()->value(),
                'createdAt' => $this->user->createdAt()->value(),
                'updatedAt' => $this->user->updatedAt()->value(),
            ]
        ];
    }
}
