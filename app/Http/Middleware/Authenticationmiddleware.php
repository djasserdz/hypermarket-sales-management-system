<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class Authenticationmiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'message' => 'Token not provided',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $accesstoken = PersonalAccessToken::findToken($token);

        if (!$accesstoken) {
            return response()->json([
                'message' => 'invalid or expired token'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = $accesstoken->tokenable;

        auth()->setUser($user);

        return $next($request);
    }
}
