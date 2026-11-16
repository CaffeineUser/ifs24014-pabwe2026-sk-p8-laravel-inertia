<?php

namespace Tests\Feature\Profile\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_the_profile_editor(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/profile')
            ->assertOk();
    }

    public function test_user_can_update_their_profile_and_keep_the_same_email(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'owner@example.test',
        ]);

        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'Updated Name',
            'email' => 'owner@example.test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Profil berhasil diperbarui.');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'owner@example.test',
        ]);
    }

    public function test_profile_update_rejects_an_email_owned_by_another_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create(['email' => 'taken@example.test']);

        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'Changed Name',
            'email' => $otherUser->email,
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $user->name,
        ]);
    }
}
