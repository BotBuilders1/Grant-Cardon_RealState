<?php

namespace App\Http\Middleware;

use Closure;

class FrameHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // return $next($request);
        $response = $next($request);
        $response->header('Content-Security-Policy', "frame-ancestors 'self' https://www.botbuilders.com");
        $response->header('X-Frame-Options', ['SAMEORIGIN', 'ALLOW FROM https://www.botbuilders.com']);
        return $response;
    }
}
