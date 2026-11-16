<?php

namespace Tests\Feature\Http\Middleware;

use App\Http\Middleware\HandleInertiaRequests;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class HandleInertiaRequestsTest extends TestCase
{
    use RefreshDatabase;

    public function test_shared_auth_and_flash_props_include_the_authenticated_user(): void
    {
        $user = User::factory()->create();
        $request = Request::create('/');
        $request->setLaravelSession($this->app['session']->driver());
        $request->session()->flash('success', 'Saved');
        $request->session()->flash('message', 'Notice');
        $request->session()->flash('error', 'Problem');
        $request->setUserResolver(fn () => $user);

        $shared = (new HandleInertiaRequests)->share($request);

        $this->assertSame([
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'profile_photo_path' => $user->profile_photo_path,
        ], $shared['auth']['user']);
        $this->assertSame('Saved', $shared['flash']['success']());
        $this->assertSame('Notice', $shared['flash']['message']());
        $this->assertSame('Problem', $shared['flash']['error']());
    }

    public function test_shared_auth_props_are_null_for_a_guest(): void
    {
        $request = Request::create('/');
        $request->setLaravelSession($this->app['session']->driver());
        $request->setUserResolver(fn () => null);

        $shared = (new HandleInertiaRequests)->share($request);

        $this->assertNull($shared['auth']['user']);
    }
}
