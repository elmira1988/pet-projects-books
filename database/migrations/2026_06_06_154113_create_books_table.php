<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Название книги
            $table->string('author'); // Автор
            $table->string('isbn')->unique(); // Уникальный международный номер
            $table->integer('price')->unsigned(); // Цена в копейках (например, 50000 = 500 руб)
            $table->integer('stock')->unsigned()->default(0); // Остаток на складе (unsigned защищает от минуса)

            $table->softDeletes();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
