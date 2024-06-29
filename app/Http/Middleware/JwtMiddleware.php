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

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $decoded = $this->jwtAuthService->decodeToken($token);

        if (!$decoded) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $claims = $decoded->claims();
        $request->attributes->add(['user' => $claims->get('sub')]);

        return $next($request);
    }
}
