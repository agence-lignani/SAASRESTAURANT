<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Models\User;
use App\Models\UserInvitation;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserInvitationAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_invitation_acceptance_creates_user_with_hashed_password(): void
    {
        $this->seed(DatabaseSeeder::class);

        $restaurant = Restaurant::query()->firstOrFail();
        $invitation = UserInvitation::query()->create([
            'restaurant_id' => $restaurant->id,
            'invited_by_user_id' => null,
            'email' => 'invite-test@example.test',
            'role' => 'editor',
            'token' => str_repeat('a', 64),
            'expires_at' => now()->addDays(7),
            'accepted_at' => null,
        ]);

        $response = $this->post('/invitation/'.$invitation->token, [
            'name' => 'Invité Test',
            'family_name' => 'Durand',
            'code' => '654321',
            'code_confirmation' => '654321',
        ]);

        $response->assertRedirect(url('/admin/login'));
        $this->assertDatabaseHas('users', ['email' => 'invite-test@example.test']);

        $user = User::query()->where('email', 'invite-test@example.test')->firstOrFail();
        $this->assertTrue(Hash::check('654321', $user->password));
        $this->assertTrue($user->restaurants()->wherePivot('role', 'editor')->exists());
    }
}
