<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Books extends Model
{
    /** @use HasFactory<\Database\Factories\BooksFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'author_id',
        'isbn',
        'description',
        'genre',
        'published_date',
        'total_copies',
        'available_copies',
        'cover_image',
        'price',
        'status',
    ];


    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }


    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowings::class);
    }


    //? Decrease available copies when a book is borrowed
    public function borrow(): void
    {
        if ($this->available_copies > 0) {
            $this->decrement('available_copies');
        }
    }

    //? Increase available copies when returned 
    public function returnBook(): void
    {
        if ($this->available_copies < $this->total_copies) {
            $this->increment('available_copies');
        }
    }
}