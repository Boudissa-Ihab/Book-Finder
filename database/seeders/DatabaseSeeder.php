<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use App\Models\UserBook;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Book::factory(50)->create();
        UserBook::factory(20)->create();
    }
}
