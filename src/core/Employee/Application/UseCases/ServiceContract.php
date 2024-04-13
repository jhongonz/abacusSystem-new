<?php

namespace Core\Employee\Application\UseCases;

use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;

interface ServiceContract
{
    public function execute(RequestService $request): null|Employee|Employees;
}
