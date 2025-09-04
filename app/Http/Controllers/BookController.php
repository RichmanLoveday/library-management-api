<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Author;
use App\Models\Books;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query =  Books::with(['author']);

        //? search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhereHas('author', function ($authorQuery) use ($search) {
                        $authorQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('bio', 'like', "%{$search}%");
                    });
            });
        }

        //? filter by genre
        if ($request->has('genre')) {
            $genre = $request->genre;
            $query->where("genre", $genre);
        }

        $books = $query->paginate(10);
        return BookResource::collection($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $book = Books::create($request->validated());
        $book->load(['author']);
        return new BookResource($book);
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id): BookResource|JsonResponse
    {
        try {
            $book = Books::with(['author'])->findOrFail($id);
            return new BookResource($book);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'The book is not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, string|int $id)
    {
        try {
            $book = Books::with(['author'])->findOrFail($id);
            $book->update($request->validated());
            return new BookResource($book);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'The book is not found'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id): JsonResponse
    {
        try {
            $book = Books::findOrFail($id);
            $book->delete();
            return response()->json([
                'status' => true,
                'message' => 'The book is deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'The book is not found'
            ], 404);
        }
    }
}