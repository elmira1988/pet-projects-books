<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomPrice = $this->fake()->numberBetween(300, 1500);
        return [
            //'title' => $this->faker->sentence(3),
            'title' => $this->fake()->realText(20),
            'author' => $this->fake()->name(),
            'isbn' => $this->fake()->isbn13(),
            // Округляем до десятков: 543 превратится в 540, а 1257 в 1260
            'price' => round($randomPrice, -1),
            'stock' => $this->fake()->numberBetween(5, 50),
        ];
    }
}
