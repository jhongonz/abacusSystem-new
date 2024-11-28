<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-08 06:39:13
 */

namespace App\Http\Controllers\ActionExecutors;

use App\Http\Exceptions\ActionExecutorNotFoundException;
use Illuminate\Http\Request;

class ActionExecutorHandler
{
    /** @var ActionExecutor[] */
    private array $actionExecutors;

    public function __construct()
    {
        $this->actionExecutors = [];
    }

    public function addActionExecutor(ActionExecutor $actionExecutor): self
    {
        $this->actionExecutors[$actionExecutor->canExecute()] = $actionExecutor;

        return $this;
    }

    /**
     * @throws ActionExecutorNotFoundException
     */
    public function invoke(string $action, Request $request): mixed
    {
        if (empty($this->actionExecutors[$action])) {
            throw new ActionExecutorNotFoundException();
        }

        return $this->actionExecutors[$action]->invoke($request);
    }
}
