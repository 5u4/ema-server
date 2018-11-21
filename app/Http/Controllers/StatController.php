<?php

namespace App\Http\Controllers;

use App\Models\Sql\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

/**
 * Class StatController
 * @package App\Http\Controllers
 */
class StatController extends Controller
{
    public function getNewUserStats(): JsonResponse
    {
        $stats = [];

        $dateLeft = 10;

        $timestamp = time();

        while ($dateLeft >= 0) {
            $currentDate = Carbon::make($timestamp);
            $timestamp -= 86400;
            $dayBeforeCurrentDate = Carbon::make($timestamp);

            $counts = User::withTrashed()
                ->whereBetween(User::CREATED_AT, [$dayBeforeCurrentDate, $currentDate])
                ->count();

            $stats[] = [
                'x' => $currentDate,
                'y' => $counts,
            ];

            $dateLeft -= 1;
        }

        return response()->json([
            'data' => [
                'newUserStat' => $stats,
            ],
        ]);
    }

    public function getActivityStats(): JsonResponse
    {
        return response()->json();
    }
}
