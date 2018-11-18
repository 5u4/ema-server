<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    
    public function show(): JsonResponse
    {
        return UserResource::make(Auth::user())->response();
    }
    
    public function index(): JsonResponse
    {
        // TODO: Add permission that only admin can view all users

        return UserResource::collection(collect(User::all()))->response();
    }

    public function search(string $input):JsonResponse
    {
        $userList = $this->userService->searchUser($input);

       // dd($userList);
        return UserResource::collection(collect($userList))->response();
    }
  

}