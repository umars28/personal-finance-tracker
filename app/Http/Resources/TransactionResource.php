<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'type' => $this->type,
            'category' => new CategoryResource($this->category),
            'description' => $this->description,
            'date' => $this->date,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}

