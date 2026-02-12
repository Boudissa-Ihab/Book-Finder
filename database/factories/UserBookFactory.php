<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserBook>
 */
class UserBookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->createRandomModel(User::class),
            'book_id' => Book::factory(),
        ];
    }

    /*
     * Instead of creating users or books for each call of this factory
     * There's a 90% chance we select a record from database instead
     * Otherwise, we create a new one and pass the ID as parameter.
     *
     * @return int
     */
    private function createRandomModel($model): int
    {
        if ($model::count() > 0 && fake()->boolean(90)) {
            return $model::inRandomOrder()->value('id');
        }

        return $model::factory()->create()->id;
    }
}
