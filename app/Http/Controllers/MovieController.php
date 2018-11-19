<?php

namespace App\Http\Controllers;

use App\Http\Requests\Movies\CreateMovieRequest;
use App\Http\Resources\MovieResource;
use App\Services\MovieService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;


/**
 * Class MovieController
 * @package App\Http\Controllers
 */
class MovieController extends Controller
{
    /** @var MovieService $movieService */
    private $movieService;

    /**
     * MovieController constructor.
     *
     * @param MovieService $movieService
     */
    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $movies = $this->movieService->getAllMoviesBelongsToUser(Auth::id());

        return MovieResource::collection(collect($movies))->response();
    }

    /**
     * @param CreateMovieRequest $request
     *
     * @return JsonResponse
     */
    public function store(CreateMovieRequest $request): JsonResponse
    {
        $movie = $this->movieService->createMovie(Auth::id(), $request->name, $request->movieId);

        return MovieResource::make($movie)->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @param int $movieId
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(int $movieId): JsonResponse
    {
        $userId = Auth::id();

        $movie = $this->movieService->getUserMovie($userId, $movieId);

        $this->movieService->detachDeleteMovie($userId, $movieId);

        return MovieResource::make($movie)->response();
    }




}
