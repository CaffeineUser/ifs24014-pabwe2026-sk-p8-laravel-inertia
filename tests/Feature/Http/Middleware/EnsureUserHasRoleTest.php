<?php

namespace Tests\Feature\Http\Middleware;

use App\Http\Middleware\EnsureUserHasRole;
use App\Models\User;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class EnsureUserHasRoleTest extends TestCase
{
    public static function deniedRoles(): array
    {
        return [
            'author' => ['author'],
            'member' => ['member'],
        ];
    }

    #[DataProvider('deniedRoles')]
    public function test_users_without_the_required_role_receive_forbidden(string $role): void
    {
        $request = Request::create('/');
        $request->setUserResolver(fn () => new User(['role' => $role]));

        $this->expectException(HttpException::class);
        (new EnsureUserHasRole)->handle($request, fn () => new Response, 'admin');
    }

    public function test_guest_users_receive_forbidden(): void
    {
        $request = Request::create('/');
        $request->setUserResolver(fn () => null);

        $this->expectException(HttpException::class);
        (new EnsureUserHasRole)->handle($request, fn () => new Response, 'admin');
    }

    public function test_users_with_the_required_role_continue_to_the_next_middleware(): void
    {
        $request = Request::create('/');
        $request->setUserResolver(fn () => new User(['role' => 'admin']));
        $response = new Response('allowed');

        $result = (new EnsureUserHasRole)->handle($request, fn () => $response, 'admin');

        $this->assertSame($response, $result);
    }
}
