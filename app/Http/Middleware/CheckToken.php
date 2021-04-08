<?php

namespace App\Http\Middleware;

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
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 401);
    }
}
