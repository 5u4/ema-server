<?php

namespace App\Http\Controllers;

use App\Http\Requests\Avatar\UploadAvatarRequest;
use App\Http\Resources\UserResource;
use App\Models\Sql\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AvatarController
 * @package App\Http\Controllers
 */
class AvatarController extends Controller
{
    /**
     * @param User $user
     *
     * @return BinaryFileResponse
     */
    public function show(User $user): BinaryFileResponse
    {
        if (isset($user->avatar_path) === false) {
            throw new NotFoundHttpException('User does not have an avatar yet.');
        }

        return response()->download(storage_path($user->avatar_path), $user->avatar_name);
    }

    /**
     * @param UploadAvatarRequest $request
     *
     * @return JsonResponse
     */
    public function store(UploadAvatarRequest $request): JsonResponse
    {
        $avatar = $request->file('avatar');

        $path = 'users' . DIRECTORY_SEPARATOR . Auth::id() . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR;

        $fileName = $avatar->hashName();

        $avatar->storeAs($path, $fileName);

        /** @var User $user */
        $user = Auth::user();
        $user->avatar_name = $avatar->getClientOriginalName();
        $user->avatar_path = 'app' . DIRECTORY_SEPARATOR . $path . $fileName;
        $user->save();

        return UserResource::make($user)->response();
    }
}
