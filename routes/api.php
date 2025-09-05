<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\MemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('authors', AuthorController::class);
Route::apiResource('books', BookController::class);
Route::apiResource('members', MemberController::class);


Route::controller(BorrowingController::class)->group(function () {
    Route::get('borrowings/overdue/list', 'overDue');
    Route::post('borrowings/{id}/return', 'returnBook');
    Route::apiResource('borrowings', BorrowingController::class);
});

//? Statistics
Route::get('statistics', function () {
    return response()->json([
        'total_books' => \App\Models\Books::count(),
        'total_authors' => \App\Models\Author::count(),
        'total_members' => \App\Models\Members::count(),
        'total_borrowed' => \App\Models\Borrowings::where('status', 'borrowed')->count(),
        'overdue_borrowings' => \App\Models\Borrowings::where('status', 'overdue')->count(),
    ]);
});