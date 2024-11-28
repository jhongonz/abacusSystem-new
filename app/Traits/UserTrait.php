<?php

namespace App\Traits;

use Illuminate\Contracts\Hashing\Hasher;

trait UserTrait
{
    protected Hasher $hasher;

    public function makeHashPassword(string $password): string
    {
        return $this->hasher->make($password);
    }
}
