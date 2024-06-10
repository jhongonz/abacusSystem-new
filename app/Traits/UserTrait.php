<?php

namespace App\Traits;

use Illuminate\Contracts\Hashing\Hasher;

trait UserTrait
{
    private Hasher $hasher;

    public function setHasher(Hasher $hasher): void
    {
        $this->hasher = $hasher;
    }

    public function makeHashPassword(string $password): string
    {
        return $this->hasher->make($password);
    }
}
