<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifySession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->exists(['user','profile','employee'])) {

            if ($request->ajax())
            {
                return response()->json([
                    'error'=>'unauthorized',
                    'error_description'=>'Failed authentication',
                    'reason'=>401
                ],401);
            }

            return redirect('/');
        }

        return $next($request);
    }
}
