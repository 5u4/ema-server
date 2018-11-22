<?php

namespace App\Http\Middleware;

use App\Models\Sql\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class FilterDisabledUsers
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
        /** @var User $user */
        $user = Auth::user();

        if ($user && $user->isActive() === false) {
            throw new AccessDeniedHttpException("Your account is banned. Please contact the admin to restore your account");
        }

        return $next($request);
    }
}
