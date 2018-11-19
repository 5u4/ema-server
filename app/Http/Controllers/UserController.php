<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Sql\User;
use Illuminate\Http\JsonResponse;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;


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
    
    public function index(): JsonResponse
    {
        // TODO: Add permission that only admin can view all users

        return UserResource::collection(collect(User::all()))->response();
    }

    public function search(string $input):JsonResponse
    {
        $userList = $this->userService->searchUser($input);

        return UserResource::collection(collect($userList))->response();
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
     * @throws \Exception
     */
    public function isFollowing(User $user): JsonResponse
    {
        $following = $this->userService->isFollowing(Auth::id(), $user->id);
      
        return response()->json(['isFollowing' => $following]);
    }
}