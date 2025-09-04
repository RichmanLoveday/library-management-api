<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Borrowings extends Model
{
    /** @use HasFactory<\Database\Factories\BorrowingsFactory> */
    use HasFactory;

    protected $fillable = [
        'book_id',
        'member_id',
        'borrowed_date',
        'due_date',
        'returned_date',
        'status',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Books::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Members::class);
    }

    //? check if borrowing is overdue
    public function isOverdue(): bool
    {
        return $this->due_date < Carbon::today() && $this->status === 'borrowed';
    }
}
