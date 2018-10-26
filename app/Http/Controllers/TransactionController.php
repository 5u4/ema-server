<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\TransactionService;
use App\Http\Resources\TransactionResource;


class TransactionController extends Controller
{
    private $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function create(Request $request): JsonResponse
    {
        $transaction = $this->transactionService->create($request->amount, $request->description);

        return TransactionResource::make($transaction)->response();
    }
}
