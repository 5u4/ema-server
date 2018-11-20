<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
use App\Models\Sql\Permission;
use App\Models\Sql\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class PermissionController
 * @package App\Http\Controllers
 */
class PermissionController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return PermissionResource::collection(Permission::all())->response();
    }

    /**
     * @param User $user
     *
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        if (Auth::user()->canViewUsers() === false) {
            throw new AccessDeniedHttpException("You do not have the permission to view users");
        }

        return PermissionResource::collection($user->permissions)->response();
    }
}
