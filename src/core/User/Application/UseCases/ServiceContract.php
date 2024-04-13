<?php

namespace Core\User\Application\UseCases;

use Core\User\Domain\User;

interface ServiceContract
{
    public function execute(RequestService $request): null|User;
}
