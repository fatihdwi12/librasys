<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::pluck('id', 'name');

        $books = [
            ['Programming', 'Laravel Up & Running', 'Matt Stauffer', '9781492041214', 2019, 5],
            ['Programming', 'Clean Code', 'Robert C. Martin', '9780132350884', 2008, 3],
            ['Programming', 'Eloquent JavaScript', 'Marijn Haverbeke', '9781593279509', 2018, 4],
            ['Database', 'Learning SQL', 'Alan Beaulieu', '9781492057611', 2020, 6],
            ['Database', 'High Performance MySQL', 'Baron Schwartz', '9781492080510', 2021, 2],
            ['Networking', 'Computer Networking: A Top-Down Approach', 'James Kurose', '9780136681557', 2020, 3],
            ['Fiction', 'Laskar Pelangi', 'Andrea Hirata', '9789793062792', 2005, 5],
            ['Fiction', 'Bumi Manusia', 'Pramoedya Ananta Toer', '9789799731234', 1980, 0],
        ];

        foreach ($books as [$categoryName, $title, $author, $isbn, $year, $stock]) {
            Book::create([
                'category_id' => $categoryIds[$categoryName],
                'title' => $title,
                'author' => $author,
                'isbn' => $isbn,
                'published_year' => $year,
                'stock' => $stock,
                'description' => "Sample book: {$title}",
            ]);
        }
    }
}