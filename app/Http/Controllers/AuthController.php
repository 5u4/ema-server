<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /** @var AuthService $authService */
    private $authService;
    /** @var UserService $userService */
    private $userService;

    /**
     * AuthController constructor.
     *
     * @param AuthService $authService
     * @param UserService $userService
     */
    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    /**
     * @param RegisterRequest $request
     *
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->userService->createUserInSql($request->username, $request->email, $request->password);

        Auth::setUser($user);

        $token = $this->authService->getAuthToken();

        return UserResource::make($user)->additional(['token' => $token])->response();
    }

    /**
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        /* Fail user if password is incorrect */
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password]) === false) {
            throw new AccessDeniedHttpException("Email and password are not matched.");
        }

        return UserResource::make(Auth::user())
            ->additional(['token' => $this->authService->getAuthToken()])
            ->response();
    }
}
