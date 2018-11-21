<?php

namespace App\Http\Controllers;

use App\Http\Requests\Dining\CreateDiningRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Services\DiningService;
use App\Http\Resources\DiningResource;
use App\Http\Resources\FavRestaurantResource;
use Illuminate\Support\Facades\Auth;
use Psy\Util\Json;

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
     * @param Request $request
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function index(Request $request): JsonResponse
    {

        $location = $request->get('location') ?? '';
        $price = $request->get('price') ?? '';
        $categories = $request->get('categories') ?? '';
        $sort_by = $request->get('sort_by') ?? '';
        $attributes = $request->get('attributes') ?? '';

        $open_now = $request->get('open_now') ?? '';
        $restaurants = $this->diningService->search($location, $price,$categories,$sort_by,$attributes,$open_now);

        return DiningResource::collection(collect($restaurants))->response();
    }

    public function findFavouriteRestaurants(): JsonResponse
    {
        $userId = Auth::id();

        $restaurant = $this->diningService->getUserRestaurants($userId);

        return FavRestaurantResource::collection(collect($restaurant))->response();
    }

    public function addFavouriteRestaurants(CreateDiningRequest $request): JsonResponse
    {
        $restaurant = $this->diningService->createRestaurant(Auth::id(), $request->name, $request->rest_id);

        return FavRestaurantResource::make($restaurant)->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function destroy(string $rest_id): JsonResponse
    {
        $userId = Auth::id();

        $restaurant = $this->diningService->getDeleteUserRestaurant($userId, $rest_id);

        $this->diningService->detachDeleteRestaurant($userId, $rest_id);

        return FavRestaurantResource::make($restaurant)->response();
    }

}
