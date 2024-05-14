<?php

namespace Core\Profile\Application\UseCasesModule;

use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;

interface ServiceContract
{
    public function execute(RequestService $request): null|Module|Modules;
}
