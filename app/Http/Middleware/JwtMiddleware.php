<?php

namespace App\Http\Middleware;

use App\Services\JwtAuthService;
use App\Services\ResponseService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JwtMiddleware
{
    public function __construct(private JwtAuthService $jwtAuthService)
    {
    }

    public function handle(Request $request, Closure $next): JsonResponse|Closure
    {
        $response = new ResponseService();
        $token = $request->bearerToken();

        if (! $token) {
            return $response->error(401, 'Unauthorized');
        }

        $decoded = $this->jwtAuthService->decodeToken($token);

        if (! $decoded) {
            return $response->error(401, 'Unauthorized');
        }

        $claims = $decoded->claims();
        $request->attributes->add(['user' => $claims->get('sub')]);

        return $next($request);
    }
}
