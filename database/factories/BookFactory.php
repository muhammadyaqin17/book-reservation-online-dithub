<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titles = [
            'Laskar Pelangi', 'Bumi Manusia', 'Ayat-Ayat Cinta', 'Negeri 5 Menara',
            'Perahu Kertas', 'Dilan 1990', 'Supernova', 'Sang Pemimpi',
            'Pulang', 'Hujan', 'Laut Bercerita', 'Gadis Kretek',
            'Cantik Itu Luka', 'Ronggeng Dukuh Paruk', 'Saman', 'Tenggelamnya Kapal Van Der Wijck',
            'Di Bawah Lindungan Ka\'bah', 'Atheis', 'Belenggu', 'Siti Nurbaya'
        ];

        $authors = [
            'Andrea Hirata', 'Pramoedya Ananta Toer', 'Habiburrahman El Shirazy',
            'Ahmad Fuadi', 'Dewi Lestari', 'Pidi Baiq', 'Tere Liye',
            'Leila S. Chudori', 'Ratih Kumala', 'Y.B. Mangunwijaya', 'Ayu Utami'
        ];

        $publishers = [
            'Gramedia Pustaka Utama', 'Mizan', 'Bentang Pustaka',
            'Republika', 'Gagas Media', 'KPG', 'Erlangga'
        ];

        $title = fake()->unique()->randomElement($titles);
        $stock = fake()->numberBetween(1, 10);

        return [
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'author' => fake()->randomElement($authors),
            'isbn' => fake()->unique()->isbn13(),
            'description' => fake()->paragraphs(3, true),
            'cover_image' => null,
            'total_stock' => $stock,
            'available_stock' => $stock,
            'published_year' => fake()->year(),
            'publisher' => fake()->randomElement($publishers),
            'pages' => fake()->numberBetween(100, 500),
            'language' => 'Indonesia',
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the book is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn(array $attributes) => [
        'available_stock' => 0,
        ]);
    }
}
