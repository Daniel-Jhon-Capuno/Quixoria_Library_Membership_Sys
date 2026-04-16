<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            // Classic Literature
            [
                'title' => 'To Kill a Mockingbird',
                'author' => 'Harper Lee',
                'isbn' => null,
                'genre' => 'Fiction',
                'category' => 'Classic Literature',
                'publication_year' => 1960,
                'description' => 'A gripping tale of racial injustice and childhood innocence in the American South.',
                'total_copies' => 5,
                'available_copies' => 5,
                'is_archived' => false,
            ],
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'isbn' => null,
                'genre' => 'Dystopian',
                'category' => 'Classic Literature',
                'publication_year' => 1949,
                'description' => 'A haunting exploration of totalitarianism and surveillance in a dystopian future.',
                'total_copies' => 4,
                'available_copies' => 4,
                'is_archived' => false,
            ],
            [
                'title' => 'Pride and Prejudice',
                'author' => 'Jane Austen',
                'isbn' => null,
                'genre' => 'Romance',
                'category' => 'Classic Literature',
                'publication_year' => 1813,
                'description' => 'A timeless romance of manners and social commentary in Regency England.',
                'total_copies' => 6,
                'available_copies' => 6,
                'is_archived' => false,
            ],
            [
                'title' => 'The Great Gatsby',
                'author' => 'F. Scott Fitzgerald',
                'isbn' => null,
                'genre' => 'Fiction',
                'category' => 'Classic Literature',
                'publication_year' => 1925,
                'description' => 'An iconic novel about love, wealth, and the American Dream.',
                'total_copies' => 5,
                'available_copies' => 5,
                'is_archived' => false,
            ],

            // Science Fiction
            [
                'title' => 'Dune',
                'author' => 'Frank Herbert',
                'isbn' => null,
                'genre' => 'Science Fiction',
                'category' => 'Science Fiction & Fantasy',
                'publication_year' => 1965,
                'description' => 'An epic sci-fi tale of politics, religion, and ecology on a desert planet.',
                'total_copies' => 4,
                'available_copies' => 4,
                'is_archived' => false,
            ],
            [
                'title' => 'The Foundation',
                'author' => 'Isaac Asimov',
                'isbn' => null,
                'genre' => 'Science Fiction',
                'category' => 'Science Fiction & Fantasy',
                'publication_year' => 1951,
                'description' => 'A visionary series spanning galactic empires and the fall of civilizations.',
                'total_copies' => 3,
                'available_copies' => 3,
                'is_archived' => false,
            ],
            [
                'title' => 'Neuromancer',
                'author' => 'William Gibson',
                'isbn' => null,
                'genre' => 'Cyberpunk',
                'category' => 'Science Fiction & Fantasy',
                'publication_year' => 1984,
                'description' => 'A groundbreaking cyberpunk novel about hackers and artificial intelligence.',
                'total_copies' => 3,
                'available_copies' => 3,
                'is_archived' => false,
            ],

            // Fantasy
            [
                'title' => 'The Lord of the Rings: The Fellowship of the Ring',
                'author' => 'J.R.R. Tolkien',
                'isbn' => null,
                'genre' => 'Fantasy',
                'category' => 'Science Fiction & Fantasy',
                'publication_year' => 1954,
                'description' => 'The beginning of an epic adventure across Middle-earth.',
                'total_copies' => 7,
                'available_copies' => 7,
                'is_archived' => false,
            ],
            [
                'title' => 'A Game of Thrones',
                'author' => 'George R. R. Martin',
                'isbn' => null,
                'genre' => 'Fantasy',
                'category' => 'Science Fiction & Fantasy',
                'publication_year' => 1996,
                'description' => 'A complex political fantasy with multiple protagonists and unexpected twists.',
                'total_copies' => 5,
                'available_copies' => 5,
                'is_archived' => false,
            ],
            [
                'title' => 'The Name of the Wind',
                'author' => 'Patrick Rothfuss',
                'isbn' => null,
                'genre' => 'Fantasy',
                'category' => 'Science Fiction & Fantasy',
                'publication_year' => 2007,
                'description' => 'A mesmerizing tale of a legendary figure recounting his extraordinary past.',
                'total_copies' => 4,
                'available_copies' => 4,
                'is_archived' => false,
            ],

            // Mystery & Thriller
            [
                'title' => 'The Girl with the Dragon Tattoo',
                'author' => 'Stieg Larsson',
                'isbn' => null,
                'genre' => 'Mystery/Thriller',
                'category' => 'Mystery & Thriller',
                'publication_year' => 2005,
                'description' => 'A gripping mystery involving a journalist and a brilliant hacker.',
                'total_copies' => 4,
                'available_copies' => 4,
                'is_archived' => false,
            ],
            [
                'title' => 'The Da Vinci Code',
                'author' => 'Dan Brown',
                'isbn' => null,
                'genre' => 'Mystery/Thriller',
                'category' => 'Mystery & Thriller',
                'publication_year' => 2003,
                'description' => 'A fast-paced thriller involving art, history, and ancient secrets.',
                'total_copies' => 6,
                'available_copies' => 6,
                'is_archived' => false,
            ],
            [
                'title' => 'Sherlock Holmes: Complete Collection',
                'author' => 'Arthur Conan Doyle',
                'isbn' => null,
                'genre' => 'Mystery',
                'category' => 'Mystery & Thriller',
                'publication_year' => 1892,
                'description' => 'The complete adventures of literature\'s most famous detective.',
                'total_copies' => 3,
                'available_copies' => 3,
                'is_archived' => false,
            ],

            // Biography & Non-Fiction
            [
                'title' => 'Educated',
                'author' => 'Tara Westover',
                'isbn' => null,
                'genre' => 'Biography',
                'category' => 'Biography & Non-Fiction',
                'publication_year' => 2018,
                'description' => 'A powerful memoir about a woman raised by survivalists who seeks education.',
                'total_copies' => 3,
                'available_copies' => 3,
                'is_archived' => false,
            ],
            [
                'title' => 'Sapiens',
                'author' => 'Yuval Noah Harari',
                'isbn' => null,
                'genre' => 'Non-Fiction',
                'category' => 'Biography & Non-Fiction',
                'publication_year' => 2014,
                'description' => 'A sweeping history of humankind from the Stone Age to the present.',
                'total_copies' => 4,
                'available_copies' => 4,
                'is_archived' => false,
            ],
            [
                'title' => 'Becoming',
                'author' => 'Michelle Obama',
                'isbn' => null,
                'genre' => 'Biography',
                'category' => 'Biography & Non-Fiction',
                'publication_year' => 2018,
                'description' => 'The intimate autobiography of America\'s former First Lady.',
                'total_copies' => 5,
                'available_copies' => 5,
                'is_archived' => false,
            ],

            // Contemporary Fiction
            [
                'title' => 'The Midnight Library',
                'author' => 'Matt Haig',
                'isbn' => null,
                'genre' => 'Contemporary Fiction',
                'category' => 'Contemporary Fiction',
                'publication_year' => 2020,
                'description' => 'A magical library where you can explore the lives you might have lived.',
                'total_copies' => 4,
                'available_copies' => 4,
                'is_archived' => false,
            ],
            [
                'title' => 'It Ends with Us',
                'author' => 'Colleen Hoover',
                'isbn' => null,
                'genre' => 'Contemporary Fiction',
                'category' => 'Contemporary Fiction',
                'publication_year' => 2016,
                'description' => 'A powerful story about domestic violence and breaking cycles.',
                'total_copies' => 6,
                'available_copies' => 6,
                'is_archived' => false,
            ],
            [
                'title' => 'The Seven Husbands of Evelyn Hugo',
                'author' => 'Taylor Jenkins Reid',
                'isbn' => null,
                'genre' => 'Contemporary Fiction',
                'category' => 'Contemporary Fiction',
                'publication_year' => 2017,
                'description' => 'A reclusive Hollywood icon reveals her secrets and scandals.',
                'total_copies' => 5,
                'available_copies' => 5,
                'is_archived' => false,
            ],

            // Self-Help & Business
            [
                'title' => 'Atomic Habits',
                'author' => 'James Clear',
                'isbn' => null,
                'genre' => 'Self-Help',
                'category' => 'Self-Help & Business',
                'publication_year' => 2018,
                'description' => 'A practical guide to building good habits and breaking bad ones.',
                'total_copies' => 3,
                'available_copies' => 3,
                'is_archived' => false,
            ],
            [
                'title' => 'Thinking, Fast and Slow',
                'author' => 'Daniel Kahneman',
                'isbn' => null,
                'genre' => 'Psychology',
                'category' => 'Self-Help & Business',
                'publication_year' => 2011,
                'description' => 'An exploration of human judgment and decision-making.',
                'total_copies' => 3,
                'available_copies' => 3,
                'is_archived' => false,
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
