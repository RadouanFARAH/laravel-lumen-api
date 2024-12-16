<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use Closure;

class VerifiedMiddleware
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
        // Pre-Middleware Action

        $response = $next($request);

        // if (!auth()->user()->isActive) {
        //     return (new Controller())->liteResponse(config("code.auth.ACCOUNT_NOT_VERIFY"));
        // }

        return $response;
    }
}
