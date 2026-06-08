<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\OrderController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


/*

|--------------------------------------------------------------------------
| Публичные маршруты (Для покупателей)
|--------------------------------------------------------------------------
*/

// Получить список доступных книг для витрины (где stock > 0)
Route::get('/books', [BookController::class, 'index']);

// Оформление заказа (Покупка). Пока делаем публичным, чтобы легко протестировать в Postman/тестах
Route::post('/orders', [OrderController::class, 'store']);


/*

|--------------------------------------------------------------------------
| Административные маршруты (Для менеджеров склада)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {

    // Посмотреть вообще все книги на складе (включая те, что закончились)
    Route::get('/books', [BookController::class, 'adminIndex']);

    // Добавить новую книгу на склад
    Route::post('/books', [BookController::class, 'store']);

    // Пополнить складской остаток существующей книги
    Route::put('/books/{id}/stock', [BookController::class, 'updateStock']);

    // Посмотреть историю логов списаний/приходов конкретной книги
    Route::get('/books/{id}/logs', [BookController::class, 'showLogs']);

});
