<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Mail\Invitation;
use App\Models\Neo\Log;
use App\Models\Sql\Permission;
use App\Models\Sql\User;
use Illuminate\Http\JsonResponse;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Mail;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
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
        return UserResource::collection(User::all())->response();
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function search(Request $request):JsonResponse
    {
        $input = $request->get('input') ?? '';
        $withTrashed = $request->get('withTrashed') ?? false;

        $users = $this->userService->searchUser($input, $withTrashed);

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
     * @param UpdateUserRequest $request
     *
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        $user = Auth::user();

        $user->update($request->all());

        return UserResource::make($user)->response();
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

        Log::activity('user.follow', $following->getId());

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
        $unfollowedUser = $this->userService->unFollowUser(Auth::id(), $user->id);

        Log::activity('user.unfollow', $unfollowedUser->getId());

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
        if (Auth::id() === $user->id) {
            throw new BadRequestHttpException("You are not allowed to disable yourself");
        }

        $user->disable();

        Log::activity('user.disable', $user->id, true);

        return UserResource::make($user)->response();
    }

    /**
     * @param User $user
     *
     * @return JsonResponse
     */
    public function enable(User $user): JsonResponse
    {
        if (Auth::id() === $user->id) {
            throw new BadRequestHttpException("You are not allowed to enable yourself");
        }

        $user->enable();

        Log::activity('user.enable', $user->id, true);

        return UserResource::make($user)->response();
    }

    /**
     * @param User $user
     * @param Permission $permission
     *
     * @return JsonResponse
     */
    public function enableUserPermission(User $user, Permission $permission): JsonResponse
    {
        if (Auth::id() === $user->id) {
            throw new BadRequestHttpException("You are not allowed to modify your permissions");
        }

        if ($user->permissions->contains('id', $permission->id) === false) {
            $user->permissions()->attach($permission->id);
        }

        Log::activity('user.enable.permission', $user->id, true);

        return response()->json([
            'data' => [
                'enabled' => $user->permissions()->where('permission_id', $permission->id)->count() === 1,
            ],
        ]);
    }

    /**
     * @param User $user
     * @param Permission $permission
     *
     * @return JsonResponse
     */
    public function disableUserPermission(User $user, Permission $permission): JsonResponse
    {
        if (Auth::id() === $user->id) {
            throw new BadRequestHttpException("You are not allowed to modify your permissions");
        }

        if ($user->permissions->contains('id', $permission->id) === true) {
            $user->permissions()->detach($permission->id);
        }

        Log::activity('user.disable.permission', $user->id, true);

        return response()->json([
            'data' => [
                'enabled' => $user->permissions()->where('permission_id', $permission->id)->count() === 1,
            ],
        ]);
    }

    public function sendmail(Request $request){

       $user = Auth::user()->username;
       $friend= $request->friend;
       $item = $request->item;
        Mail::to($request->email)->send(new Invitation($user,$friend,$item));
        return "success";
    }
}
