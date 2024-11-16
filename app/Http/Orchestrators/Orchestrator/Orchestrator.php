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
     * @return array<int|string, mixed>
     */
    public function make(Request $request): array;

    public function canOrchestrate(): string;
}
