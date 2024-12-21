<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-20 23:35:27
 */

namespace App\Http\Controllers\ActionExecutors;

use Illuminate\Http\Request;

interface ActionExecutorHandlerContract
{
    public function addActionExecutor(ActionExecutor $actionExecutor): ActionExecutorHandlerContract;

    public function invoke(string $action, Request $request): mixed;
}
