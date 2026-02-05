<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->member = User::factory()->create(['role' => 'member']);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_member_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->member)->get(route('admin.dashboard'));

        // Middleware returns 403 Forbidden for non-admin users
        $response->assertForbidden();
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_books_list(): void
    {
        $category = Category::factory()->create();
        Book::factory()->count(3)->create(['category_id' => $category->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.books.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_book(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('admin.books.store'), [
            'title' => 'Test Book',
            'author' => 'Test Author',
            'category_id' => $category->id,
            'isbn' => '978-0-12-345678-9',
            'description' => 'Test description',
            'total_stock' => 5,
            'published_year' => 2024,
            'publisher' => 'Test Publisher',
            'pages' => 200,
            'language' => 'Indonesia',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.books.index'));

        $this->assertDatabaseHas('books', [
            'title' => 'Test Book',
            'author' => 'Test Author',
        ]);
    }

    public function test_admin_can_update_book(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($this->admin)->put(route('admin.books.update', $book), [
            'title' => 'Updated Title',
            'author' => $book->author,
            'category_id' => $category->id,
            'isbn' => $book->isbn,
            'total_stock' => $book->total_stock,
            'available_stock' => $book->available_stock,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.books.index'));

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_admin_can_delete_book_without_reservations(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($this->admin)->delete(route('admin.books.destroy', $book));

        $response->assertRedirect(route('admin.books.index'));

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }

    public function test_admin_can_view_users_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_categories_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.categories.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_reservations_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reservations.index'));

        $response->assertStatus(200);
    }
}
