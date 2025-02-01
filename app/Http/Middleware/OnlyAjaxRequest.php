<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Symfony\Component\HttpFoundation\Response;

class OnlyAjaxRequest
{
    public function __construct(
        private readonly Redirector $redirector,
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request): Response $next
     */
    public function handle(Request $request, \Closure $next): \Closure|Response
    {
        if (!$request->ajax()) {
            return $this->redirector->route('index');
        }

        return $next($request);
    }
}
