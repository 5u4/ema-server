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
    private const DEFAULT_DISPLAY_DAYS = 15;
    private const ONE_DAY = 86400;
    private const DATE_FORMAT = 'Y-m-d';

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
    public function getStats()
    {
        $newUserStats = [];
        $activityStats = [];

        $dateLeft = self::DEFAULT_DISPLAY_DAYS;

        $date = Carbon::today();

        $userCount = User::withTrashed()->where(User::CREATED_AT, '>', $date)->count();
        $activityCount = $this->logService->getActivityCountInGivenPeriod($date->getTimestamp(), time());

        $newUserTotal = $userCount;
        $activityTotal = $activityCount;

        $newUserStats[] = [
            'x' => $date->format(self::DATE_FORMAT),
            'y' => $userCount,
        ];

        $activityStats[] = [
            'x' => $date->format(self::DATE_FORMAT),
            'y' => $activityCount,
        ];

        $timestamp = Carbon::yesterday()->getTimestamp();

        while ($dateLeft >= 0) {
            $counts = DB::table('stats')->where('timestamp', $timestamp)->first(['new_user_count', 'activity_count']);

            if ($counts === null) {
                /* Get new user count */
                $date = Carbon::createFromTimestamp($timestamp);
                $dateBefore = Carbon::createFromTimestamp($timestamp - self::ONE_DAY);

                $newUserCount = User::withTrashed()
                    ->whereBetween(User::CREATED_AT, [$dateBefore, $date])->count();

                /* Get activity count */
                $activityCount = $this->logService->getActivityCountInGivenPeriod($timestamp - self::ONE_DAY, $timestamp);

                DB::table('stats')->updateOrInsert(['timestamp' => $timestamp], [
                    'new_user_count' => $newUserCount,
                    'activity_count' => $activityCount,
                ]);
            } else {
                $newUserCount = $counts->new_user_count;
                $activityCount = $counts->activity_count;
            }

            $x = date(self::DATE_FORMAT, $timestamp);

            $newUserStats[] = [
                'x' => $x,
                'y' => $newUserCount,
            ];

            $newUserTotal += $newUserCount;

            $activityStats[] = [
                'x' => $x,
                'y' => $activityCount,
            ];

            $activityTotal += $activityCount;

            $timestamp -= self::ONE_DAY;
            $dateLeft--;
        }

        return response()->json([
            'data' => [
                'newUserTotal' => $newUserTotal,
                'activityTotal' => $activityTotal,
                'newUserStats' => array_reverse($newUserStats),
                'activityStats' => array_reverse($activityStats),
            ],
        ]);
    }
}
