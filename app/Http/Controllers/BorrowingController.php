<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBorrowingRequest;
use App\Http\Resources\BorrowingResource;
use App\Models\Books;
use App\Models\Borrowings;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Borrowings::with(['book', 'member']);

        //? search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('book', function ($bookQuery) use ($search) {
                    $bookQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                })->orWhereHas('member', function ($memberQuery) use ($search) {
                    $memberQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        //? filter by status
        if ($request->has('status')) {
            $status = $request->status;
            $query->where("status", $status);
        }

        $borrowings = $query->paginate(10);
        return BorrowingResource::collection($borrowings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBorrowingRequest $request)
    {
        try {
            $book = Books::findOrFail($request->book_id);

            //? check if book is available for borrowing
            if (!$book->isAvailable()) {
                return response()->json([
                    'status' => false,
                    'message' => "Book is not available for borrowing"
                ], 422);
            }

            //? update book available copies
            $book->borrow();

            $borrowing = Borrowings::create($request->validated());
            $borrowing->load(['book', 'member']);
            return new BorrowingResource($borrowing);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create borrowing: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}