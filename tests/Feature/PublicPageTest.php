<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_can_be_rendered(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_books_catalog_can_be_rendered(): void
    {
        $response = $this->get('/books');

        $response->assertStatus(200);
    }

    public function test_book_detail_page_can_be_rendered(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $response = $this->get("/books/{$book->slug}");

        $response->assertStatus(200);
        $response->assertSee($book->title);
    }

    public function test_books_can_be_filtered_by_category(): void
    {
        $category1 = Category::factory()->create(['name' => 'Fiksi']);
        $category2 = Category::factory()->create(['name' => 'Non-Fiksi']);

        Book::factory()->create(['category_id' => $category1->id, 'is_active' => true, 'title' => 'Book Fiksi']);
        Book::factory()->create(['category_id' => $category2->id, 'is_active' => true, 'title' => 'Book Non-Fiksi']);

        $response = $this->get('/books?category=' . $category1->slug);

        $response->assertStatus(200);
        $response->assertSee('Book Fiksi');
    }

    public function test_books_can_be_searched(): void
    {
        $category = Category::factory()->create();
        Book::factory()->create([
            'category_id' => $category->id,
            'title' => 'Laskar Pelangi',
            'is_active' => true,
        ]);
        Book::factory()->create([
            'category_id' => $category->id,
            'title' => 'Bumi Manusia',
            'is_active' => true,
        ]);

        $response = $this->get('/books?search=Laskar');

        $response->assertStatus(200);
        $response->assertSee('Laskar Pelangi');
    }

    public function test_book_availability_api_returns_json(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create([
            'category_id' => $category->id,
            'available_stock' => 5,
        ]);

        $response = $this->getJson("/books/{$book->slug}/availability");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'available',
            'available_stock',
            'queue_length',
            'cached',
        ]);
    }

    public function test_inactive_book_returns_404(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create([
            'category_id' => $category->id,
            'is_active' => false,
        ]);

        $response = $this->get("/books/{$book->slug}");

        $response->assertStatus(404);
    }
}
