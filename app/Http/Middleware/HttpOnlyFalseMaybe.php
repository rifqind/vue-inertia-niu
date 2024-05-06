<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cookie;


class HttpOnlyFalseMaybe
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        // Get the CSRF token
        $token = $request->session()->token();

        // Set the CSRF token cookie with httpOnly option set to false
        $response->headers->setCookie(
            Cookie::make('XSRF-TOKEN', $token, 0, '/', null, false, false) // Set httpOnly to false
        );
        return $response;
    }
}
