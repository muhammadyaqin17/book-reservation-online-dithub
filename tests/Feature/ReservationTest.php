<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    protected User $member;
    protected Book $book;

    protected function setUp(): void
    {
        parent::setUp();

        $this->member = User::factory()->create(['role' => 'member']);
        $category = Category::factory()->create();
        $this->book = Book::factory()->create([
            'category_id' => $category->id,
            'available_stock' => 5,
            'total_stock' => 5,
            'is_active' => true,
        ]);
    }

    public function test_member_can_view_reservation_form(): void
    {
        $response = $this->actingAs($this->member)->get(route('reservations.create', $this->book));

        $response->assertStatus(200);
    }

    public function test_member_can_create_reservation(): void
    {
        $response = $this->actingAs($this->member)->post(route('reservations.store', $this->book), [
            'notes' => 'Test notes',
        ]);

        $response->assertRedirect(route('reservations.my'));

        $this->assertDatabaseHas('reservations', [
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => 'pending',
        ]);
    }

    public function test_member_can_view_their_reservations(): void
    {
        Reservation::factory()->create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
        ]);

        $response = $this->actingAs($this->member)->get(route('reservations.my'));

        $response->assertStatus(200);
    }

    public function test_member_can_cancel_pending_reservation(): void
    {
        $reservation = Reservation::factory()->create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->member)->post(route('reservations.cancel', $reservation));

        $response->assertRedirect(route('reservations.my'));

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_member_cannot_cancel_borrowed_reservation(): void
    {
        $reservation = Reservation::factory()->create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => 'borrowed',
        ]);

        $response = $this->actingAs($this->member)->post(route('reservations.cancel', $reservation));

        $response->assertRedirect();

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'borrowed',
        ]);
    }

    public function test_member_cannot_see_other_users_reservations(): void
    {
        $otherUser = User::factory()->create(['role' => 'member']);
        $reservation = Reservation::factory()->create([
            'user_id' => $otherUser->id,
            'book_id' => $this->book->id,
        ]);

        $response = $this->actingAs($this->member)->get(route('reservations.my'));

        $response->assertDontSee($otherUser->name);
    }

    public function test_guest_cannot_access_reservation_form(): void
    {
        $response = $this->get(route('reservations.create', $this->book));

        $response->assertRedirect(route('login'));
    }

    public function test_reservation_with_no_stock_enters_queue(): void
    {
        $this->book->update(['available_stock' => 0]);

        $response = $this->actingAs($this->member)->post(route('reservations.store', $this->book), [
            'notes' => 'Test notes',
        ]);

        $response->assertRedirect(route('reservations.my'));

        $this->assertDatabaseHas('reservations', [
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => 'pending',
            'queue_position' => 1,
        ]);
    }
}
