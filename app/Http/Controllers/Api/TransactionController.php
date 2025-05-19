<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Repositories\TransactionRepositoryInterface;
use Cache;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $repository;

    public function __construct(TransactionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

        /**
     * @OA\Get(
     *     path="/api/transactions",
     *     summary="List all transactions",
     *     tags={"Transaction"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of transactions",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Transactions retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=123),
     *                     @OA\Property(property="user_id", type="integer", example=45),
     *                     @OA\Property(property="amount", type="number", format="float", example=1000.50),
     *                     @OA\Property(property="status", type="string", example="completed"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-18T10:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-18T10:00:00Z")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $transactions = $this->repository->all($request);

        return response()->json([
            'message' => 'Transaction list retrieved successfully',
            'data' => TransactionResource::collection($transactions),
        ]);
    }

        /**
     * @OA\Post(
     *     path="/api/transactions",
     *     summary="Create new transaction",
     *     tags={"Transaction"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id","amount","status"},
     *             @OA\Property(property="user_id", type="integer", example=45),
     *             @OA\Property(property="amount", type="number", format="float", example=1000.50),
     *             @OA\Property(property="status", type="string", example="pending")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Transaction created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Transaction created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=123),
     *                 @OA\Property(property="user_id", type="integer", example=45),
     *                 @OA\Property(property="amount", type="number", format="float", example=1000.50),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-18T10:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-18T10:00:00Z")
     *             )
     *         )
     *     )
     * )
     */
    public function store(TransactionRequest $request)
    {
        $transaction = $this->repository->store($request->validated());

        $this->invalidateSummaryCache(auth()->id(), $transaction->date);

        return response()->json([
            'message' => 'Transaction created successfully',
            'data' => new TransactionResource($transaction),
        ], 201);
    }

        /**
     * @OA\Get(
     *     path="/api/transactions/{id}",
     *     summary="Show single transaction",
     *     tags={"Transaction"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Transaction retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=123),
     *                 @OA\Property(property="user_id", type="integer", example=45),
     *                 @OA\Property(property="amount", type="number", format="float", example=1000.50),
     *                 @OA\Property(property="status", type="string", example="completed"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-18T10:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-18T10:00:00Z")
     *             )
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $transaction = $this->repository->show($id);

        return response()->json([
            'message' => 'Transaction retrieved successfully',
            'data' => new TransactionResource($transaction),
        ]);
    }

        /**
     * @OA\Put(
     *     path="/api/transactions/{id}",
     *     summary="Update transaction",
     *     tags={"Transaction"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id","amount","status"},
     *             @OA\Property(property="user_id", type="integer", example=45),
     *             @OA\Property(property="amount", type="number", format="float", example=1500.75),
     *             @OA\Property(property="status", type="string", example="completed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Transaction updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=123),
     *                 @OA\Property(property="user_id", type="integer", example=45),
     *                 @OA\Property(property="amount", type="number", format="float", example=1500.75),
     *                 @OA\Property(property="status", type="string", example="completed"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-18T10:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-18T10:00:00Z")
     *             )
     *         )
     *     )
     * )
     */
    public function update(TransactionRequest $request, $id)
    {
        $transaction = $this->repository->update($id, $request->validated());

        $this->invalidateSummaryCache(auth()->id(), $transaction->date);

        return response()->json([
            'message' => 'Transaction updated successfully',
            'data' => new TransactionResource($transaction),
        ]);
    }

        /**
     * @OA\Delete(
     *     path="/api/transactions/{id}",
     *     summary="Delete transaction",
     *     tags={"Transaction"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Transaction deleted successfully")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        $transactionDate = $transaction->date;

        $this->repository->delete($id);

        $this->invalidateSummaryCache(auth()->id(), $transactionDate);

        return response()->json([
            'message' => 'Transaction deleted successfully',
        ]);

    }

    /**
 * @OA\Get(
 *     path="/api/transactions/filter",
 *     summary="Filter transactions by category and type",
 *     tags={"Transaction"},
 *     security={{"sanctum": {}}},
 *     @OA\Parameter(
 *         name="category_id",
 *         in="query",
 *         description="ID of the category to filter",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="type",
 *         in="query",
 *         description="Transaction type (e.g. expense, income)",
 *         required=false,
 *         @OA\Schema(type="string", enum={"expense", "income"})
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Filtered list of transactions",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="user_id", type="integer", example=10),
 *                     @OA\Property(property="category_id", type="integer", example=3),
 *                     @OA\Property(property="amount", type="number", format="float", example=80000.00),
 *                     @OA\Property(property="type", type="string", example="expense"),
 *                     @OA\Property(property="note", type="string", example="Lunch expense"),
 *                     @OA\Property(property="date", type="string", format="date", example="2025-05-18"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-18T09:00:00Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-18T09:00:00Z"),
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */
    public function filter(Request $request)
    {
        $filters = $request->only(['category_id', 'type']);
        $transactions = $this->repository->filter($filters);

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    /**
 * @OA\Get(
 *     path="/api/summary/monthly",
 *     summary="Get user's monthly transaction summary",
 *     tags={"Summary"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="month",
 *         in="query",
 *         description="Month in numeric format (e.g. 5 or 05)",
 *         required=false,
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Parameter(
 *         name="year",
 *         in="query",
 *         description="Year (e.g. 2024)",
 *         required=false,
 *         @OA\Schema(type="integer", example=2024)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Monthly summary retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="total_income", type="number", format="float", example=5000000),
 *                 @OA\Property(property="total_expense", type="number", format="float", example=2000000),
 *                 @OA\Property(property="ending_balance", type="number", format="float", example=3000000),
 *                 @OA\Property(
 *                     property="transactions_per_category",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="category_id", type="integer", example=1),
 *                         @OA\Property(property="category_name", type="string", example="Food"),
 *                         @OA\Property(property="transaction_count", type="integer", example=5)
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */
    public function monthlySummary(Request $request)
    {
        $user = auth()->user();

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $month = str_pad($month, 2, '0', STR_PAD_LEFT); // misalnya 5 => 05
        $cacheKey = "summary:user:{$user->id}:{$year}-{$month}";

        $summary = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($user, $year, $month) {
            $transactions = $user->transactions()
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();

            $totalIncome = $transactions->where('type', 'income')->sum('amount');
            $totalExpense = $transactions->where('type', 'expense')->sum('amount');
            $endingBalance = $totalIncome - $totalExpense;

            $transactionsPerCategory = $transactions->groupBy('category_id')->map(function ($items, $categoryId) {
                return [
                    'category_id' => (int) $categoryId,
                    'category_name' => optional($items->first()->category)->name,
                    'transaction_count' => $items->count(),
                ];
            })->values();

            return [
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'ending_balance' => $endingBalance,
                'transactions_per_category' => $transactionsPerCategory,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    protected function invalidateSummaryCache($userId, $date)
    {
        $month = \Illuminate\Support\Carbon::parse($date)->format('m');
        $year = \Illuminate\Support\Carbon::parse($date)->format('Y');
        $cacheKey = "summary:user:{$userId}:{$year}-{$month}";
        \Illuminate\Support\Facades\Cache::forget($cacheKey);
    }

}
