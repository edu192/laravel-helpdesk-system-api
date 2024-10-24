<?php

use App\Http\Controllers\Backend\CommentController as BackendCommentController;
use App\Http\Controllers\Backend\TicketController as BackendTicketController;
use App\Http\Controllers\Frontend\Ticket\Comment\CommentController;
use App\Http\Controllers\Frontend\Ticket\MediaController;
use App\Http\Controllers\Frontend\Ticket\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => 'auth:sanctum'], function () {
    //Auth user routes
    Route::group(['as' => 'user.'], function () {

        Route::group(['prefix' => 'tickets', 'as' => 'ticket.'], function () {

            //Ticket routes
            Route::get('/', [TicketController::class, 'index'])->name('index');
            Route::post('/', [TicketController::class, 'store'])->name('store');
            Route::get('/{ticket}', [TicketController::class, 'show'])->name('show');
            Route::put('/{ticket}', [TicketController::class, 'update'])->name('update');
            Route::delete('/{ticket}', [TicketController::class, 'destroy'])->name('destroy');

            //Ticket comments routes
            Route::group(['prefix' => '{ticket}/comments', 'as' => 'comment.'], function () {
                Route::get('/', [CommentController::class, 'index'])->name('index');
                Route::post('/', [CommentController::class, 'store'])->name('store');
                Route::get('/{comment}', [CommentController::class, 'show'])->name('show');
                Route::put('/{comment}', [CommentController::class, 'update'])->name('update');
                Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('destroy');
            });

            //File routes
            Route::group(['prefix' => '{ticket}/files', 'as' => 'file.'], function () {
                Route::get('/', [MediaController::class, 'index'])->name('index');
                Route::post('/', [MediaController::class, 'store'])->name('store');
                Route::get('/{media}', [MediaController::class, 'show'])->name('show');
                Route::delete('/{media}', [MediaController::class, 'destroy'])->name('destroy');
            });
        });
    });
    //Backend routes
    Route::group(['prefix' => 'support','middleware' => 'backend', 'as' => 'support.'], function () {
        //Ticket routes
        Route::group(['prefix' => 'tickets', 'as' => 'ticket.'], function () {
            Route::get('/', [BackendTicketController::class, 'index'])->name('index');
            Route::get('/{ticket}', [BackendTicketController::class, 'show'])->name('show');
            Route::put('/{ticket}', [BackendTicketController::class, 'update'])->name('update');
            //Ticket comments routes
            Route::group(['prefix' => '{ticket}/comments', 'as' => 'comment.'], function () {
                Route::get('/', [BackendCommentController::class, 'index'])->name('index');
                Route::post('/', [BackendCommentController::class, 'store'])->name('store');
                Route::get('/{comment}', [BackendCommentController::class, 'show'])->name('show');
                Route::put('/{comment}', [BackendCommentController::class, 'update'])->name('update');
                Route::delete('/{comment}', [BackendCommentController::class, 'destroy'])->name('destroy');
            });
        });
    });
});