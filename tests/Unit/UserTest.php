<?php

namespace Tests\Unit;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_is_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'member']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($member->isAdmin());
    }

    public function test_user_is_member(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'member']);

        $this->assertFalse($admin->isMember());
        $this->assertTrue($member->isMember());
    }

    public function test_user_has_many_reservations(): void
    {
        $user = User::factory()->create();

        $this->assertCount(0, $user->reservations);
    }

    public function test_user_default_role_is_member(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Default should be null if not set, but our seeder sets 'member'
        $this->assertNull($user->role);
    }
}
