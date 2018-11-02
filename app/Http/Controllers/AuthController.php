<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Sql\User;
use App\Services\AuthService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $user = DB::transaction(function () use ($request) {
            $user = $this->userService->createUserInSql($request->username, $request->email, $request->password);

            $this->userService->createUserInNeo($user->id);

            Auth::setUser($user);

            return $user;
        });

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

        /** @var User $user */
        $user = Auth::user();
        $user->last_login = Carbon::now();
        $user->save();

        return UserResource::make($user)
            ->additional(['token' => $this->authService->getAuthToken()])
            ->response();
    }
}
