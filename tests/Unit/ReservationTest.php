<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reservation_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $this->assertInstanceOf(User::class , $reservation->user);
        $this->assertEquals($user->id, $reservation->user->id);
    }

    public function test_reservation_belongs_to_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $this->assertInstanceOf(Book::class , $reservation->book);
        $this->assertEquals($book->id, $reservation->book->id);
    }

    public function test_reservation_can_be_cancelled_when_pending(): void
    {
        $reservation = Reservation::factory()->create(['status' => 'pending']);
        $this->assertTrue($reservation->canBeCancelled());
    }

    public function test_reservation_can_be_cancelled_when_approved(): void
    {
        $reservation = Reservation::factory()->create(['status' => 'approved']);
        $this->assertTrue($reservation->canBeCancelled());
    }

    public function test_reservation_cannot_be_cancelled_when_borrowed(): void
    {
        $reservation = Reservation::factory()->create(['status' => 'borrowed']);
        $this->assertFalse($reservation->canBeCancelled());
    }

    public function test_reservation_is_in_queue_when_has_queue_position(): void
    {
        $reservation = Reservation::factory()->create([
            'status' => 'pending',
            'queue_position' => 2,
        ]);

        $this->assertTrue($reservation->isInQueue());
    }

    public function test_reservation_is_overdue_when_due_date_passed(): void
    {
        $reservation = Reservation::factory()->create([
            'status' => 'borrowed',
            'due_date' => now()->subDays(1),
        ]);

        $this->assertTrue($reservation->isOverdue());
    }

    public function test_reservation_is_not_overdue_when_due_date_in_future(): void
    {
        $reservation = Reservation::factory()->create([
            'status' => 'borrowed',
            'due_date' => now()->addDays(7),
        ]);

        $this->assertFalse($reservation->isOverdue());
    }

    public function test_approve_reservation_updates_status(): void
    {
        $reservation = Reservation::factory()->create(['status' => 'pending']);

        $reservation->approve();

        $this->assertEquals('approved', $reservation->fresh()->status);
        $this->assertNotNull($reservation->fresh()->approved_at);
    }

    public function test_mark_as_borrowed_updates_status_and_decrements_stock(): void
    {
        $book = Book::factory()->create(['available_stock' => 5]);
        $reservation = Reservation::factory()->create([
            'book_id' => $book->id,
            'status' => 'approved',
        ]);

        $reservation->markAsBorrowed();

        $this->assertEquals('borrowed', $reservation->fresh()->status);
        $this->assertNotNull($reservation->fresh()->borrowed_at);
        $this->assertNotNull($reservation->fresh()->due_date);
        $this->assertEquals(4, $book->fresh()->available_stock);
    }

    public function test_mark_as_returned_updates_status_and_increments_stock(): void
    {
        $book = Book::factory()->create(['available_stock' => 4, 'total_stock' => 5]);
        $reservation = Reservation::factory()->create([
            'book_id' => $book->id,
            'status' => 'borrowed',
        ]);

        $reservation->markAsReturned();

        $this->assertEquals('returned', $reservation->fresh()->status);
        $this->assertNotNull($reservation->fresh()->returned_at);
        $this->assertEquals(5, $book->fresh()->available_stock);
    }

    public function test_status_label_returns_indonesian_label(): void
    {
        $reservation = Reservation::factory()->create(['status' => 'pending']);
        $this->assertEquals('Menunggu Konfirmasi', $reservation->status_label);

        $reservation->status = 'approved';
        $this->assertEquals('Disetujui', $reservation->status_label);
    }
}
