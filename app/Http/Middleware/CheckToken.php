<?php

namespace App\Http\Middleware;

use App\Exceptions\ControllerException;
use Closure;

class CheckToken
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
        $user = auth()->user();
        if($user !== null && auth()->payload()->get('type') !== 'refresh') {
            return $next($request);
        }
        return new ControllerException("Unauthorized", 401);
    }
}
