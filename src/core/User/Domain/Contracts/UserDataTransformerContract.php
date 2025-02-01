<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Domain\Contracts;

use Core\User\Domain\User;

interface UserDataTransformerContract
{
    public function write(User $user): UserDataTransformerContract;

    /**
     * @return array<string, mixed>
     */
    public function read(): array;
}
