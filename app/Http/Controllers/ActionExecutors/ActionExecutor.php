<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-08 06:31:30
 */

namespace App\Http\Controllers\ActionExecutors;

use Illuminate\Http\Request;

interface ActionExecutor
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function invoke(Request $request): mixed;

    /**
     * @return string
     */
    public function canExecute(): string;
}
