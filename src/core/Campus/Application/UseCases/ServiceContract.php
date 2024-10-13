<?php

namespace Core\Campus\Application\UseCases;

use Core\Campus\Domain\Campus;
use Core\Campus\Domain\CampusCollection;

interface ServiceContract
{
    public function execute(RequestService $request): null|Campus|CampusCollection;
}
