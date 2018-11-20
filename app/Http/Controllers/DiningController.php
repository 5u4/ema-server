<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\DiningService;
use App\Http\Resources\DiningResource;
use Illuminate\Support\Facades\Auth;

/**
 * Class DiningController
 * @package App\Http\Controllers
 */
class DiningController extends Controller
{
    private $diningService;

    public function __construct(DiningService $diningService)
    {
        $this->diningService = $diningService;
    }

    /**
     * @param String $input
     * @return JsonResponse
     */
    public function index(String $input): JsonResponse
    {
        $restaurantList = $this->diningService->getRestaurantList($input);
        return DiningResource::collection(collect($restaurantList))->response();
    }

}
