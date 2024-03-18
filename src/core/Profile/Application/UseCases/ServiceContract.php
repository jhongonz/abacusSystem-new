<?php

namespace Core\Profile\Application\UseCases;

use Core\Profile\Domain\Profile;
use Core\Profile\Domain\Profiles;

interface ServiceContract
{
    public function execute(RequestService $request): null|Profile|Profiles;
}