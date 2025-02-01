<?php

namespace Core\Profile\Domain\Contracts;

use Core\Profile\Domain\Profile;

interface ProfileDataTransformerContract
{
    public function write(Profile $profile): ProfileDataTransformerContract;

    /**
     * @return array<string, mixed>
     */
    public function read(): array;

    /**
     * @return array<string, mixed>
     */
    public function readToShare(): array;
}
