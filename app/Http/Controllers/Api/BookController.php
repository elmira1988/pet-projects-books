<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
class BookController extends Controller
{
    /**
     * 🟢 Для покупателей: Список доступных книг (stock > 0)
     */
    public function index(): JsonResponse
    {
        // Выводим по 6 книг на страницу
        $books = Book::where('stock', '>', 0)
            ->orderBy('title')
            ->paginate(6);

        return response()->json([
            'success' => true,
            // Метод paginate возвращает не просто массив, а объект с метаданными (текущая страница, сколько всего страниц и т.д.)
            'catalog' => $books
        ]);
    }

    /**
     * 🔵 Для менеджеров: Список вообще всех книг на складе
     */
    public function adminIndex(): JsonResponse
    {
        // Менеджер должен видеть всё, даже пустые позиции
        $books = Book::orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $books
        ]);
    }

    /**
     * 🔵 Для менеджеров: Добавление новой книги на склад
     */
    public function store(Request $request): JsonResponse
    {
        // Валидация входящих данных — обязательный стандарт Middle-разработчика
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn', // ISBN должен быть уникальным
            'price' => 'required|integer|min:0', // Целое число рублей
            'stock' => 'required|integer|min:0',
        ]);

        $book = Book::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Книга успешно добавлена на склад',
            'data' => $book
        ], 201); // 201 - статус успешного создания в REST API
    }
}
