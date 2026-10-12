<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Programming', 'description' => 'Software development and coding'],
            ['name' => 'Database', 'description' => 'Database design and SQL'],
            ['name' => 'Networking', 'description' => 'Computer networks and security'],
            ['name' => 'Fiction', 'description' => 'Novels and short stories'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}