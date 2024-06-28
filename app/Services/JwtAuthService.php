<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\JwtToken;
use Illuminate\Support\Str;

class JwtAuthService
{
    private $secretKey;

    public function __construct()
    {
        $this->secretKey = env('JWT_SECRET');
    }

    public function generateToken($user)
    {
        $uniqueId = Str::uuid()->toString();
        $payload = [
            'iss' => "your-issuer", // Issuer
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + 60*60, // Expiration time (1 hour)
            'jti' => $uniqueId // JWT ID
        ];

        $token = JWT::encode($payload, $this->secretKey, 'HS256');

        JwtToken::create([
            'user_id' => $user->id,
            'unique_id' => $uniqueId,
            'token_title' => 'user_token',
            'expires_at' => now()->addHour(),
        ]);

        return $token;
    }

    public function decodeToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            $storedToken = JwtToken::where('unique_id', $decoded->jti)->first();

            if (!$storedToken) {
                return null;
            }

            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function revokeToken($token)
    {
        $decoded = $this->decodeToken($token);

        if ($decoded) {
            JwtToken::where('unique_id', $decoded->jti)->delete();
        }
    }
}
