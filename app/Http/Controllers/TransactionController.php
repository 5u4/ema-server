<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\TransactionService;
use App\Http\Resources\TransactionResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Expense\CreateTransactionRequest;


class TransactionController extends Controller
{
    private $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(): JsonResponse
    {
        $user = Auth::user();
        $userId = $user->id;

        $transactions = $this->transactionService->getAll($userId);

        return TransactionResource::collection(collect($transactions))->response();

    }

    public function create(CreateTransactionRequest $request): JsonResponse
    {
        // get $user id from current session
        $user = Auth::user();
        $userId = $user->id;

        // create a transaction and create a relationship to current user in Neo
        $transaction = $this->transactionService->create($userId, $request->amount, $request->description);

        return TransactionResource::make($transaction)->response();

    }

    public function show(int $id)
    {
        $transaction = $this->transactionService->getTransactionById($id);

        return TransactionResource::make($transaction)->response();

    }
}
