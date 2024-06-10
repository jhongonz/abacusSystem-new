<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Symfony\Component\HttpFoundation\Response;

/**
 * @codeCoverageIgnore
 */
class OnlyAjaxRequest
{
    private Redirector $redirector;

    public function __construct(
        Redirector $redirector
    ) {
        $this->redirector = $redirector;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Closure|Response
    {
        if (! $request->ajax()) {
            return $this->redirector->route('index');
        }

        return $next($request);
    }
}
