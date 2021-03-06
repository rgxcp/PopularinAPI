<?php

namespace App\Http\Middleware;

use App\Developer;
use Closure;

class IsDeveloper
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
        $isDeveloper = Developer::where('api_key', $request->header('API-Key'))->exists();

        if (!$isDeveloper) {
            return response()->json([
                'status' => 919,
                'message' => 'Unauthenticated Developer',
                'problem' => 'You must sign up or sign in as developer to use all the feature of this API'
            ]);
        }

        return $next($request);
    }
}
