<?php

namespace Tests\Feature\Auth\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_register_pages_are_available_to_guests(): void
    {
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_member_login_redirects_to_their_dashboard(): void
    {
        $user = User::factory()->create([
            'username' => 'member-user',
            'password' => Hash::make('secret-password'),
            'role' => 'member',
        ]);

        $response = $this->post('/login', [
            'username' => 'member-user',
            'password' => 'secret-password',
        ]);

        $response->assertRedirect('/dashboard/posts');
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_login_redirects_to_the_admin_dashboard(): void
    {
        $user = User::factory()->admin()->create([
            'username' => 'admin-user',
            'password' => Hash::make('secret-password'),
        ]);

        $response = $this->post('/login', [
            'username' => 'admin-user',
            'password' => 'secret-password',
            'remember' => false,
        ]);

        $response->assertRedirect('/admin/posts');
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_login_returns_a_username_error(): void
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'unknown-user',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ]);
        $this->assertGuest();
    }

    public function test_login_requires_both_credentials(): void
    {
        $response = $this->from('/login')->post('/login');

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['username', 'password']);
    }

    public function test_registration_creates_and_authenticates_an_author(): void
    {
        $response = $this->post('/register', [
            'name' => 'New Author',
            'username' => 'new-author',
            'email' => 'new-author@example.test',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ]);

        $response->assertRedirect(route('dashboard.posts.index'));
        $response->assertSessionHas('success', 'Pendaftaran berhasil! Selamat datang.');
        $this->assertDatabaseHas('users', [
            'username' => 'new-author',
            'role' => 'author',
        ]);
        $this->assertAuthenticated();
    }

    public function test_logout_invalidates_the_authenticated_session(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
