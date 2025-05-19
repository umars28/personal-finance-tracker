<?php 
namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function all(Request $request)
    {
        $query = Transaction::with('category')->where('user_id', auth()->id());

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        return $query->latest()->get();
    }

    public function store(array $data)
    {
        \Log::info('Current user id: ' . auth()->id());

        $data['user_id'] = auth()->id();
        return Transaction::create($data);
    }

    public function show(int $id)
    {
        return Transaction::with('category')->where('user_id', auth()->id())->findOrFail($id);
    }

    public function update(int $id, array $data)
    {
        $transaction = Transaction::where('user_id', auth()->id())->findOrFail($id);
        $transaction->update($data);
        return $transaction;
    }

    public function delete(int $id)
    {
        $transaction = Transaction::where('user_id', auth()->id())->findOrFail($id);
        return $transaction->delete();
    }

    public function filter(array $filters)
    {
        $query = Transaction::query();

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->get();
    }
}
