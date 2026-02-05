<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'queue_position' => null,
            'status' => Reservation::STATUS_PENDING,
            'reserved_at' => now(),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the reservation is approved.
     */
    public function approved(): static
    {
        return $this->state(fn(array $attributes) => [
        'status' => Reservation::STATUS_APPROVED,
        'approved_at' => now(),
        ]);
    }

    /**
     * Indicate that the book is borrowed.
     */
    public function borrowed(): static
    {
        return $this->state(fn(array $attributes) => [
        'status' => Reservation::STATUS_BORROWED,
        'approved_at' => now()->subDays(2),
        'borrowed_at' => now()->subDay(),
        'due_date' => now()->addDays(13),
        ]);
    }

    /**
     * Indicate that the book is returned.
     */
    public function returned(): static
    {
        return $this->state(fn(array $attributes) => [
        'status' => Reservation::STATUS_RETURNED,
        'approved_at' => now()->subDays(10),
        'borrowed_at' => now()->subDays(9),
        'due_date' => now()->addDays(5),
        'returned_at' => now(),
        ]);
    }

    /**
     * Indicate that the reservation is in queue.
     */
    public function inQueue(int $position = 1): static
    {
        return $this->state(fn(array $attributes) => [
        'status' => Reservation::STATUS_PENDING,
        'queue_position' => $position,
        ]);
    }
}
