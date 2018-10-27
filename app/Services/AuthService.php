<?php

namespace App\Services;

use App\Models\Sql\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

/**
 * Class AuthService
 * @package App\Services
 */
class AuthService
{
    private const JWT_ALGORITHM = 'HS256';

    /**
     * Generate a jwt for current auth user
     *
     * @return string
     */
    public function getAuthToken(): string
    {
        /** @var User $user */
        $user = Auth::user();

        $issuedAt = Carbon::now()->timestamp;

        return JWT::encode([
            'iss'         => config('auth.jwt.iss'),
            'aud'         => config('auth.jwt.aud'),
            'iat'         => $issuedAt,
            'exp'         => $issuedAt + config('auth.jwt.ttl'),
            'userId'      => $user->id,
            'permissions' => $user->permissions,
        ], config('auth.jwt.key'), self::JWT_ALGORITHM);
    }

    /**
     * Verify a token
     *
     * @param string $token
     *
     * @return bool
     */
    public function verifyAuthToken(?string $token): bool
    {
        if (config('auth.enabled') == false) {
            return true;
        }

        if (is_null($token)) {
            throw new UnauthorizedException();
        }

        $decoded = JWT::decode($token, config('auth.jwt.key'), [self::JWT_ALGORITHM]);

        Auth::setUser(User::find($decoded->userId));

        return true;
    }

    /**
     * Decode a token
     *
     * @param string $token
     *
     * @return array
     */
    public function decodeAuthToken(string $token): array
    {
        return (array)JWT::decode($token, config('auth.jwt.key'), ['HS256']);
    }
}
