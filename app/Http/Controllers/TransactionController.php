<?php

namespace App\Http\Controllers;

use App\Models\Neo\Log;
use Illuminate\Http\JsonResponse;
use App\Services\TransactionService;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Expense\CreateTransactionRequest;

/**
 * Class TransactionController
 * @package App\Http\Controllers
 */
class TransactionController extends Controller
{
    /** @var TransactionService $transactionService */
    private $transactionService;

    /**
     * TransactionController constructor.
     *
     * @param TransactionService $transactionService
     */
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $transactions = $this->transactionService->getAllTransactions(Auth::id());

        return TransactionResource::collection(collect($transactions))->response();
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $order = $request->order ?? $this->transactionService::DEFAULT_TRANSACTION_SEARCH_ORDER;

        $transactions = $this->transactionService->getAllTransactions(Auth::id(), $order);

        $filteredTransactions = $this->transactionService->filterTransactionsWithFragments(
            $transactions, $request->fragment ?? ''
        );

        $metaData = $request->withMeta
            ? $this->transactionService->getMetaDataFromGivenTransactions($filteredTransactions)
            : null;

        return TransactionResource::collection(collect($filteredTransactions))->additional(['meta' => $metaData])->response();
    }

    /**
     * @param CreateTransactionRequest $request
     *
     * @return JsonResponse
     */
    public function create(CreateTransactionRequest $request): JsonResponse
    {
        $tags = $request->tags ? explode(',', $request->tags) : [];

        $transaction = $this->transactionService->createTransaction(
            Auth::id(), $request->amount, $request->description, $request->timestamp ?? time(), $tags
        );

        Log::activity('transaction.create');

        return TransactionResource::make($transaction)->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @param CreateTransactionRequest $request
     *
     * @return JsonResponse
     */
    public function update(int $id, CreateTransactionRequest $request): JsonResponse
    {
        $tags = $request->tags ? explode(',', $request->tags) : [];

        $transaction = $this->transactionService->updateTransactionById(
            Auth::id(), $id, $request->amount, $request->description, $request->timestamp ?? null, $tags ?? []
        );

        Log::activity('transaction.update');

        return TransactionResource::make($transaction)->response();
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $transaction = $this->transactionService->getUserTransactionById(Auth::id(), $id);

        return TransactionResource::make($transaction)->response();
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function delete(int $id): JsonResponse
    {
        $userId = Auth::id();

        $transaction = $this->transactionService->getUserTransactionById($userId, $id);

        $this->transactionService->deleteTransactionById($userId, $id);

        Log::activity('transaction.delete');

        return TransactionResource::make($transaction)->response();
    }
}
