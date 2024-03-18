<?php

namespace Core\Profile\Domain\Contracts;

use Core\Profile\Domain\Profile;

interface ProfileDataTransformerContract
{
    public function write(Profile $profile): ProfileDataTransformerContract;

    public function read(): array;
    
    public function readToShare(): array;
}