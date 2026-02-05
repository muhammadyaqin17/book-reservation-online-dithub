<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@bookreservation.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        // Create Sample Member
        User::create([
            'name' => 'Member Demo',
            'email' => 'member@bookreservation.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_MEMBER,
        ]);

        // Create Categories
        $categories = [
            ['name' => 'Fiksi', 'slug' => 'fiksi', 'description' => 'Novel, cerpen, dan karya fiksi lainnya', 'icon' => 'bi-book'],
            ['name' => 'Non-Fiksi', 'slug' => 'non-fiksi', 'description' => 'Buku berdasarkan fakta dan realita', 'icon' => 'bi-journal-text'],
            ['name' => 'Sains & Teknologi', 'slug' => 'sains-teknologi', 'description' => 'Buku tentang sains dan teknologi', 'icon' => 'bi-cpu'],
            ['name' => 'Sejarah', 'slug' => 'sejarah', 'description' => 'Buku sejarah Indonesia dan dunia', 'icon' => 'bi-clock-history'],
            ['name' => 'Pendidikan', 'slug' => 'pendidikan', 'description' => 'Buku pelajaran dan pendidikan', 'icon' => 'bi-mortarboard'],
            ['name' => 'Agama', 'slug' => 'agama', 'description' => 'Buku keagamaan', 'icon' => 'bi-star'],
            ['name' => 'Ekonomi & Bisnis', 'slug' => 'ekonomi-bisnis', 'description' => 'Buku ekonomi dan bisnis', 'icon' => 'bi-graph-up'],
            ['name' => 'Kesehatan', 'slug' => 'kesehatan', 'description' => 'Buku tentang kesehatan', 'icon' => 'bi-heart-pulse'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create Sample Books
        $books = [
            // Fiksi
            ['category_id' => 1, 'title' => 'Laskar Pelangi', 'author' => 'Andrea Hirata', 'isbn' => '9789793062792', 'total_stock' => 5, 'available_stock' => 3, 'published_year' => 2005, 'publisher' => 'Bentang Pustaka', 'pages' => 529],
            ['category_id' => 1, 'title' => 'Bumi Manusia', 'author' => 'Pramoedya Ananta Toer', 'isbn' => '9789799731234', 'total_stock' => 3, 'available_stock' => 2, 'published_year' => 1980, 'publisher' => 'Hasta Mitra', 'pages' => 535],
            ['category_id' => 1, 'title' => 'Ayat-Ayat Cinta', 'author' => 'Habiburrahman El Shirazy', 'isbn' => '9789791227889', 'total_stock' => 4, 'available_stock' => 4, 'published_year' => 2004, 'publisher' => 'Republika', 'pages' => 418],
            ['category_id' => 1, 'title' => 'Perahu Kertas', 'author' => 'Dewi Lestari', 'isbn' => '9789792232516', 'total_stock' => 3, 'available_stock' => 1, 'published_year' => 2009, 'publisher' => 'Bentang Pustaka', 'pages' => 444],
            ['category_id' => 1, 'title' => 'Dilan 1990', 'author' => 'Pidi Baiq', 'isbn' => '9786023850976', 'total_stock' => 6, 'available_stock' => 5, 'published_year' => 2014, 'publisher' => 'Mizan', 'pages' => 332],

            // Non-Fiksi
            ['category_id' => 2, 'title' => 'Sapiens: Riwayat Singkat Umat Manusia', 'author' => 'Yuval Noah Harari', 'isbn' => '9786024246945', 'total_stock' => 3, 'available_stock' => 2, 'published_year' => 2017, 'publisher' => 'Gramedia', 'pages' => 564],
            ['category_id' => 2, 'title' => 'Atomic Habits', 'author' => 'James Clear', 'isbn' => '9786020633176', 'total_stock' => 5, 'available_stock' => 3, 'published_year' => 2019, 'publisher' => 'Gramedia', 'pages' => 352],

            // Sains & Teknologi
            ['category_id' => 3, 'title' => 'A Brief History of Time', 'author' => 'Stephen Hawking', 'isbn' => '9780553380163', 'total_stock' => 2, 'available_stock' => 2, 'published_year' => 1988, 'publisher' => 'Bantam', 'pages' => 256],
            ['category_id' => 3, 'title' => 'The Innovators', 'author' => 'Walter Isaacson', 'isbn' => '9781476708690', 'total_stock' => 2, 'available_stock' => 1, 'published_year' => 2014, 'publisher' => 'Simon & Schuster', 'pages' => 560],

            // Sejarah
            ['category_id' => 4, 'title' => 'Sejarah Indonesia Modern', 'author' => 'M.C. Ricklefs', 'isbn' => '9789791486392', 'total_stock' => 3, 'available_stock' => 3, 'published_year' => 2008, 'publisher' => 'Serambi', 'pages' => 784],
            ['category_id' => 4, 'title' => 'Indonesia Dalam Arus Sejarah', 'author' => 'Tim Penulis', 'isbn' => '9789790151234', 'total_stock' => 4, 'available_stock' => 2, 'published_year' => 2012, 'publisher' => 'Kemendikbud', 'pages' => 680],

            // Pendidikan
            ['category_id' => 5, 'title' => 'Filsafat Pendidikan', 'author' => 'Prof. Dr. Made Pidarta', 'isbn' => '9789790101234', 'total_stock' => 4, 'available_stock' => 4, 'published_year' => 2007, 'publisher' => 'Rineka Cipta', 'pages' => 320],

            // Agama
            ['category_id' => 6, 'title' => 'Fiqih Sunnah', 'author' => 'Sayyid Sabiq', 'isbn' => '9789793233451', 'total_stock' => 3, 'available_stock' => 2, 'published_year' => 2006, 'publisher' => 'Pena Pundi Aksara', 'pages' => 856],

            // Ekonomi
            ['category_id' => 7, 'title' => 'Rich Dad Poor Dad', 'author' => 'Robert Kiyosaki', 'isbn' => '9786020332178', 'total_stock' => 4, 'available_stock' => 3, 'published_year' => 2018, 'publisher' => 'Gramedia', 'pages' => 264],

            // Kesehatan
            ['category_id' => 8, 'title' => 'The Body: A Guide for Occupants', 'author' => 'Bill Bryson', 'isbn' => '9780385539302', 'total_stock' => 2, 'available_stock' => 2, 'published_year' => 2019, 'publisher' => 'Doubleday', 'pages' => 464],
        ];

        foreach ($books as $bookData) {
            $bookData['slug'] = \Illuminate\Support\Str::slug($bookData['title']);
            $bookData['description'] = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.';
            $bookData['language'] = 'Indonesia';
            $bookData['is_active'] = true;
            Book::create($bookData);
        }
    }
}
