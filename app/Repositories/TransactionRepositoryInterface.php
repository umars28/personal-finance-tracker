<?php 
namespace App\Repositories;

use Illuminate\Http\Request;

interface TransactionRepositoryInterface
{
    public function all(Request $request);
    public function store(array $data);
    public function show(int $id);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function filter(array $filters);

}
