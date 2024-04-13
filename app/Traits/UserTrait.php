<?php

namespace App\Traits;

use Illuminate\Support\Facades\Hash;

trait UserTrait {
    public function makeHashPassword(string $password): string
    {
        return Hash::make($password);
    }
}
