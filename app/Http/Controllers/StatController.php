<?php

namespace App\Http\Controllers;

use App\Models\Sql\User;
use App\Services\LogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class StatController
 * @package App\Http\Controllers
 */
class StatController extends Controller
{
    private const DEFAULT_DISPLAY_DAYS = 30;
    private const ONE_DAY = 86400;

    /** @var LogService $logService */
    private $logService;

    /**
     * StatController constructor.
     *
     * @param LogService $logService
     */
    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * @return JsonResponse
     */
    public function getNewUserStats(): JsonResponse
    {
        $stats = [];

        $dateLeft = self::DEFAULT_DISPLAY_DAYS;

        $date = Carbon::today();

        $stats[] = [
            'x' => $date->getTimestamp(),
            'y' => User::withTrashed()->where(User::CREATED_AT, '>', $date)->count(),
        ];

        $timestamp = Carbon::yesterday()->getTimestamp();

        while ($dateLeft >= 0) {
            $count = DB::table('stats')->where('timestamp', $timestamp)->first(['new_user_count']);

            if ($count === null) {
                $date = Carbon::createFromTimestamp($timestamp);
                $dateBefore = Carbon::createFromTimestamp($timestamp - self::ONE_DAY);

                $count = User::withTrashed()
                    ->whereBetween(User::CREATED_AT, [$dateBefore, $date])->count();

                DB::table('stats')->updateOrInsert(['timestamp' => $timestamp], ['new_user_count' => $count]);
            }

            $stats[] = [
                'x' => $timestamp,
                'y' => $count,
            ];

            $timestamp -= self::ONE_DAY;
            $dateLeft--;
        }

        return response()->json([
            'data' => [
                'newUserStats' => $stats,
            ],
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getActivityStats(): JsonResponse
    {
        $stats = [];

        $dateLeft = self::DEFAULT_DISPLAY_DAYS;

        $date = Carbon::today();

        $stats[] = [
            'x' => $date->getTimestamp(),
            'y' => $this->logService->getActivityCountInGivenPeriod($date->getTimestamp(), time()),
        ];

        $timestamp = Carbon::yesterday()->getTimestamp();

        while ($dateLeft >= 0) {
            $count = DB::table('stats')->where('timestamp', $timestamp)->first(['activity_count']);

            if ($count === null) {
                $count = $this->logService->getActivityCountInGivenPeriod($timestamp - self::ONE_DAY, $timestamp);

                DB::table('stats')->updateOrInsert(['timestamp' => $timestamp], ['activity_count' => $count]);
            }

            $stats[] = [
                'x' => $timestamp,
                'y' => $count,
            ];

            $timestamp -= self::ONE_DAY;
            $dateLeft--;
        }

        return response()->json([
            'data' => [
                'activityStats' => $stats,
            ],
        ]);
    }
}
