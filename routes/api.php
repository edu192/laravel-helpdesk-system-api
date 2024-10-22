<?php

use App\Http\Controllers\Frontend\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'ticket', 'as' => 'user.ticket.'], function () {
    //auth user
    Route::get('/', [TicketController::class, 'index'])->name('index');
    Route::post('/', [TicketController::class, 'store'])->name('store');
    Route::get('/{ticket}', [TicketController::class, 'show'])->name('show');
    Route::put('/{ticket}', [TicketController::class, 'update'])->name('update');
    Route::delete('/{ticket}', [TicketController::class, 'destroy'])->name('destroy');

    //comments
    Route::group(['prefix' => 'comment', 'as' => 'comment.'], function () {
        Route::post('/{ticket}', [TicketController::class, 'post_comment'])->name('store');
        Route::delete('/{ticket}', [TicketController::class, 'destroy'])->name('destroy');
    });
});