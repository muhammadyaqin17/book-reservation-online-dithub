<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Category;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_belongs_to_category(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class , $book->category);
        $this->assertEquals($category->id, $book->category->id);
    }

    public function test_book_has_many_reservations(): void
    {
        $book = Book::factory()->create();
        $user = User::factory()->create();

        Reservation::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(Reservation::class , $book->reservations->first());
    }

    public function test_book_is_available_when_stock_greater_than_zero(): void
    {
        $book = Book::factory()->create(['available_stock' => 5]);
        $this->assertTrue($book->isAvailable());

        $book->available_stock = 0;
        $this->assertFalse($book->isAvailable());
    }

    public function test_decrement_stock_reduces_available_stock(): void
    {
        $book = Book::factory()->create(['available_stock' => 5, 'total_stock' => 5]);

        $book->decrementStock();

        $this->assertEquals(4, $book->fresh()->available_stock);
    }

    public function test_increment_stock_increases_available_stock(): void
    {
        $book = Book::factory()->create(['available_stock' => 3, 'total_stock' => 5]);

        $book->incrementStock();

        $this->assertEquals(4, $book->fresh()->available_stock);
    }

    public function test_increment_stock_does_not_exceed_total_stock(): void
    {
        $book = Book::factory()->create(['available_stock' => 5, 'total_stock' => 5]);

        $book->incrementStock();

        $this->assertEquals(5, $book->fresh()->available_stock);
    }

    public function test_book_scope_available_returns_only_available_books(): void
    {
        Book::factory()->create(['available_stock' => 5, 'is_active' => true]);
        Book::factory()->create(['available_stock' => 0, 'is_active' => true]);
        Book::factory()->create(['available_stock' => 3, 'is_active' => false]);

        $availableBooks = Book::available()->get();

        $this->assertCount(1, $availableBooks);
    }

    public function test_book_uses_slug_as_route_key(): void
    {
        $book = Book::factory()->create(['slug' => 'test-book']);

        $this->assertEquals('slug', $book->getRouteKeyName());
    }
}
