<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\JwtAuthService;
use App\Services\ResponseService;
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
        $response = new ResponseService();
        $token = $request->bearerToken();

        if (!$token) return $response->error(401, "Unauthorized");

        $decoded = $this->jwtAuthService->decodeToken($token);

        if (!$decoded) return $response->error(401, "Unauthorized");

        $claims = $decoded->claims();
        $request->attributes->add(['user' => $claims->get('sub')]);

        return $next($request);
    }
}
