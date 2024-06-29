<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\JwtAuthService;
use Illuminate\Http\Request;

class JwtMiddleware
{
    protected $jwtAuthService;

    public function __construct(JwtAuthService $jwtAuthService)
    {
        $this->jwtAuthService = $jwtAuthService;
    }

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        dd($request->all());

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        $decoded = $this->jwtAuthService->decodeToken($token);

        if (!$decoded) {
            return response()->json(['error' => 'Invalid or revoked token'], 401);
        }

        $request->attributes->add(['user' => $decoded->sub]);

        return $next($request);
    }
}
