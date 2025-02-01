<?php

namespace App\Traits;

use Illuminate\Contracts\Hashing\Hasher;

trait UserTrait
{
    protected Hasher $hasher;

    private function makeHashPassword(string $password): string
    {
        return $this->hasher->make($password);
    }
}
