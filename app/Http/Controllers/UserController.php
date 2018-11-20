<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\UpdateUserPermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\UserResource;
use App\Models\Sql\User;
use Illuminate\Http\JsonResponse;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param User $user
     *
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        $id = $user->id;

        $relationships = [
            'followings' => UserResource::collection(collect($this->userService->getUserFollowingsInSql($id))),
            'followers' => UserResource::collection(collect($this->userService->getUserFollowersInSql($id))),
        ];

        return UserResource::make($user)->additional($relationships)->response();
    }
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        if (Auth::user()->canViewUsers() === false) {
            throw new AccessDeniedHttpException("You do not have the permission to view users");
        }

        return UserResource::collection(User::withTrashed()->get())->response();
    }

    /**
     * @param string $input
     *
     * @return JsonResponse
     */
    public function search(string $input):JsonResponse
    {
        $users = $this->userService->searchUser($input);

        return UserResource::collection($users)->response();
    }

    /**
     * @return JsonResponse
     */
    public function commonfriends(): JsonResponse
    {
        $users = $this->userService->getCommonFriends(Auth::id());

        return UserResource::collection(collect($users))->response();
    }


    /**
     * @return JsonResponse
     */
    public function friends(): JsonResponse
    {
        $users = $this->userService->getFriends(Auth::id());

        return UserResource::collection(collect($users))->response();
    }

    /**
     * @param User $user
     *
     * @return JsonResponse
     */
    public function follow(User $user): JsonResponse
    {
        $following = $this->userService->followUser(Auth::id(), $user->id);

        return UserResource::make(User::find($following->getSqlId()))->response();
    }

    /**
     * @param User $user
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function unfollow(User $user): JsonResponse
    {
        $this->userService->unFollowUser(Auth::id(), $user->id);

        return UserResource::make($user)->response();
    }

    /**
     * @param User $user
     *
     * @return JsonResponse
     */
    public function isFollowing(User $user): JsonResponse
    {
        $following = $this->userService->isFollowing(Auth::id(), $user->id);
      
        return response()->json(['isFollowing' => $following]);
    }

    /**
     * @param User $user
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function disable(User $user): JsonResponse
    {
        if (Auth::user()->canDisableUsers() === false) {
            throw new AccessDeniedHttpException("You do not have the permission to disable users");
        }

        if (Auth::id() === $user->id) {
            throw new BadRequestHttpException("You are not allowed to disable yourself");
        }

        $user->delete();

        return UserResource::make($user)->response();
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function enable(int $id): JsonResponse
    {
        if (Auth::user()->canDisableUsers() === false) {
            throw new AccessDeniedHttpException("You do not have the permission to enable users");
        }

        if (Auth::id() === $id) {
            throw new BadRequestHttpException("You are not allowed to enable yourself");
        }

        $user = User::onlyTrashed()->findOrFail($id);

        $user->restore();

        return UserResource::make($user)->response();
    }

    /**
     * @param UpdateUserPermissionRequest $request
     * @param User $user
     *
     * @return JsonResponse
     */
    public function updateUserPermissions(UpdateUserPermissionRequest $request, User $user): JsonResponse
    {
        if (Auth::user()->canUpdateUserPermissions() === false) {
            throw new AccessDeniedHttpException("You do not have the permission to modify users");
        }

        if (Auth::id() === $user->id) {
            throw new BadRequestHttpException("You are not allowed to modify your permissions");
        }

        $user->permissions()->sync($request->permissions);

        return UserResource::make($user)->additional([
            'permissions' => PermissionResource::collection($user->permissions),
        ])->response();
    }
}
