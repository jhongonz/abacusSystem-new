<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Application\UseCases;

use Core\User\Domain\User;

interface ServiceContract
{
    public function execute(RequestService $request): null|User;
}
