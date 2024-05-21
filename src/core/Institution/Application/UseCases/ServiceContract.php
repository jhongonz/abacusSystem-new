<?php

namespace Core\Institution\Application\UseCases;

use Core\Institution\Domain\Institution;
use Core\Institution\Domain\Institutions;

interface ServiceContract
{
    public function execute(RequestService $request): null|Institution|Institutions;
}
