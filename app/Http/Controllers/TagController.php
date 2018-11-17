<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tags\CreateTagRequest;
use App\Http\Resources\TagResource;
use App\Services\TagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class TagController
 * @package App\Http\Controllers
 */
class TagController extends Controller
{
    /** @var TagService $tagService */
    private $tagService;

    /**
     * TagController constructor.
     *
     * @param TagService $tagService
     */
    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $tags = $this->tagService->getAllTagsBelongsToUser(Auth::id());

        return TagResource::collection(collect($tags))->response();
    }

    /**
     * @param CreateTagRequest $request
     *
     * @return JsonResponse
     */
    public function store(CreateTagRequest $request): JsonResponse
    {
        $tag = $this->tagService->createTag(Auth::id(), $request->name);

        return TagResource::make($tag)->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @param CreateTagRequest $request
     * @param int $tagId
     *
     * @return JsonResponse
     *
     * NOTE: Since tag only have one attribute, UpdateTagRequest is the same as CreateTagRequest
     */
    public function update(CreateTagRequest $request, int $tagId): JsonResponse
    {
        $tag = $this->tagService->updateTag(Auth::id(), $tagId, $request->name);

        return TagResource::make($tag)->response();
    }

    /**
     * @param int $tagId
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(int $tagId): JsonResponse
    {
        $userId = Auth::id();

        $tag = $this->tagService->getUserTag($userId, $tagId);

        $this->tagService->detachDeleteTag($userId, $tagId);

        return TagResource::make($tag)->response();
    }
}
