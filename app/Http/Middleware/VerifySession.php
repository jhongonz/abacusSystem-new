<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Symfony\Component\HttpFoundation\Response;

class VerifySession
{
    public function __construct(
        private readonly Redirector $redirector,
        private readonly Session $session
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->session->exists(['user', 'profile', 'employee'])) {

            if ($request->ajax()) {
                return new JsonResponse(
                    [
                        'error' => 'unauthorized',
                        'error_description' => 'Failed authentication',
                        'reason' => 401,
                    ],
                    Response::HTTP_UNAUTHORIZED
                );
            }

            return $this->redirector->to('/');
        }

        return $next($request);
    }
}
