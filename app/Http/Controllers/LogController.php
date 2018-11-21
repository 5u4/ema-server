<?php

namespace App\Http\Controllers;

use App\Http\Requests\Logs\ShowLogRequest;
use App\Http\Resources\LogFlowResource;
use App\Models\Sql\User;
use App\Services\LogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class LogController extends Controller
{
    /** @var LogService $logService */
    private $logService;

    /**
     * LogController constructor.
     *
     * @param LogService $logService
     */
    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * @param ShowLogRequest $request
     *
     * @return JsonResponse
     */
    public function index(ShowLogRequest $request): JsonResponse
    {
        if (Auth::user()->canViewUsers() === false) {
            throw new AccessDeniedHttpException("You do not have the permission to view users");
        }

        $limit  = $request->perPage ?? $this->logService::DEFAULT_LOG_LIMITS;
        $offset = $request->page ? ($request->page - 1) * $limit : 0;

        $logs = $this->logService->showAllLogs($limit, $offset);

        return LogFlowResource::collection(collect($logs))->response();
    }

    /**
     * @param ShowLogRequest $request
     * @param User $user
     *
     * @return JsonResponse
     */
    public function show(ShowLogRequest $request, User $user): JsonResponse
    {
        if (Auth::user()->canViewUsers() === false && Auth::id() !== $user->id) {
            throw new AccessDeniedHttpException("You do not have the permission to view users");
        }

        $limit  = $request->perPage ?? $this->logService::DEFAULT_LOG_LIMITS;
        $offset = $request->page ? ($request->page - 1) * $limit : 0;

        $logs = $this->logService->showGivenUserLogs($user->id, $limit, $offset);

        return LogFlowResource::collection(collect($logs))->response();
    }
}
