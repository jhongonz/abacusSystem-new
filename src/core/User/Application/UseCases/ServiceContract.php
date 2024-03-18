<?php

namespace Core\User\Application\UseCases;

interface ServiceContract
{
    public function execute(RequestService $request);
}