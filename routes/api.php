<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\MemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//? Authentication routes
Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('v1')->group(function () {
        //? book, authors, members, borrowings routes
        Route::apiResource('authors', AuthorController::class);
        Route::apiResource('books', BookController::class);
        Route::apiResource('members', MemberController::class);


        //? Borrowings routes
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

        //? Auth user routes
        Route::controller(AuthController::class)->group(function () {
            Route::get('/user', 'user');
            Route::post('/logout', 'logout');
        });
    });


    //? version 2 routes
    Route::prefix("v2")->group(function () {
        Route::apiResource('authors', AuthorController::class);
        Route::get("books/get-five-books", [BookController::class, 'getFiveBooks']);
        Route::apiResource('books', BookController::class);
        Route::apiResource('members', MemberController::class);


        //? Borrowings routes
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

        //? Auth user routes
        Route::controller(AuthController::class)->group(function () {
            Route::get('/user', 'user');
            Route::post('/logout', 'logout');
        });
    });
});