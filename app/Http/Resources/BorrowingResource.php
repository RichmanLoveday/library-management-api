<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BorrowingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'member' => new MemberResource($this->whenLoaded('member')),
            'book' => new BookResource($this->whenLoaded('book')),
            'borrowed_date' => $this->borrowed_date,
            'due_date' => $this->due_date,
            'return_date' => $this->return_date,
            'is_overdue' => $this->isOverdue(),
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}