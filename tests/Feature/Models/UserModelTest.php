<?php

namespace Tests\Feature\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_status_and_password_cast_are_applied(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'plain-password']);
        $author = User::factory()->create();

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($author->isAdmin());
        $this->assertNotSame('plain-password', $admin->getRawOriginal('password'));
    }
}
