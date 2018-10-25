<?php

namespace App\Services;

use App\Models\Sql\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthService
 * @package App\Services
 */
class AuthService
{
    /**
     * Generate a jwt for current auth user
     *
     * @return string
     */
    public function getAuthToken(): string
    {
        /** @var User $user */
        $user = Auth::user();

        return JWT::encode([
            'iss'         => config('auth.jwt.iss'),
            'aud'         => config('auth.jwt.aud'),
            'iat'         => time(),
            'nbf'         => time() + config('auth.jwt.ttl'),
            'userId'      => $user->id,
            'permissions' => $user->permissions,
        ], config('auth.jwt.key'));
    }
}
