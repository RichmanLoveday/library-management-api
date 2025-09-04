<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Books>
 */
class BooksFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'author_id' => Author::inRandomOrder()->first()->id ?? Author::factory()->create()->id,
            'isbn' => $this->faker->unique()->isbn13(),
            'description' => $this->faker->paragraph(),
            'genre' => $this->faker->randomElement(['Fiction', 'Non-fiction', 'Sci-Fi', 'Fantasy', 'Mystery']),
            'total_copies' => $this->faker->numberBetween(1, 50),
            'available_copies' => $this->faker->numberBetween(0, 50),
            'price' => $this->faker->randomFloat(2, 5, 200),
            'cover_image' => $this->faker->imageUrl(200, 300, 'books', true),
            'published_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}