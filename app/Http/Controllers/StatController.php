<?php

namespace App\Http\Controllers;

use App\Services\StatService;
use Illuminate\Http\JsonResponse;

/**
 * Class StatController
 * @package App\Http\Controllers
 */
class StatController extends Controller
{
    /** @var StatService $statService */
    private $statService;

    /**
     * StatController constructor.
     *
     * @param StatService $statService
     */
    public function __construct(StatService $statService)
    {
        $this->statService = $statService;
    }

    /**
     * @return JsonResponse
     */
    public function getStats()
    {
        return response()->json([
            'data' => $this->statService->buildStatsGraph(),
        ]);
    }
}
