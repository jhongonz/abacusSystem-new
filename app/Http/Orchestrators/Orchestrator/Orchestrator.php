<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-04 12:17:44
 */

namespace App\Http\Orchestrators\Orchestrator;

use Illuminate\Http\Request;

interface Orchestrator
{
    /**
     * @param Request $request
     */
    public function make(Request $request);

    /**
     * @return string
     */
    public function canOrchestrate(): string;
}
