<?php

namespace Core\Employee\Application\UseCases;

use Core\Employee\Domain\Employee;

interface ServiceContract
{
    public function execute(RequestService $request): null|Employee;
}