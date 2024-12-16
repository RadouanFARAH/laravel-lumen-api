<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        $locale = 'es'; // to get from $request later
        App::setLocale($locale);
        
        return $next($request);
    }
}
