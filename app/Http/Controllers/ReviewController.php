<?php

namespace App\Http\Controllers;

use App\Http\Requests\Review\CreateReviewRequest;
use App\Http\Resources\ReviewSource;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class ReviewController
 * @package App\Http\Controllers
 */
class ReviewController extends Controller
{
    /** @var ReviewService $reviewService */
    private $reviewService;

    /**
     * ReviewController constructor.
     *
     * @param ReviewService $reviewService
     */
    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     *@param int $movieId
     *
     * @return JsonResponse
     */
    public function index(int $movieId): JsonResponse
    {
        $reviews = $this->reviewService->getAllReviewBelongsToMovie($movieId);

        return ReviewSource::collection(collect($reviews))->response();
    }

    /**
     * @param CreateReviewRequest $request
     *
     * @return JsonResponse
     */
    public function store(CreateReviewRequest $request): JsonResponse
    {
        $review = $this->reviewService->createReview(Auth::id(), $request->reviewTitle, $request->reviewContent, $request->movieId, $request->rate);

        return ReviewSource::make($review)->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @param int $reviewId
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(int $reviewId): JsonResponse
    {
        $review = $this->reviewService->getMovieReview($reviewId);

        $this->reviewService->detachDeleteReview($reviewId);

        return ReviewSource::make($review)->response();
    }
}
