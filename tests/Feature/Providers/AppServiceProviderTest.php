<?php

namespace Tests\Feature\Providers;

use App\Features\Post\Contracts\PostRepositoryInterface;
use App\Features\Post\Repositories\PostRepository;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AppServiceProviderTest extends TestCase
{
    public function test_repository_contract_is_bound_to_the_repository(): void
    {
        (new AppServiceProvider($this->app))->register();

        $this->assertInstanceOf(PostRepository::class, $this->app->make(PostRepositoryInterface::class));
    }

    public function test_boot_forces_https_for_each_forwarded_or_secure_request_signal(): void
    {
        config(['app.url' => 'http://example.test']);
        $request = request();
        $provider = new AppServiceProvider($this->app);

        $request->headers->set('X-Forwarded-Proto', 'https');
        $provider->boot();
        $this->assertSame('https://', URL::formatScheme('http'));

        $request->headers->remove('X-Forwarded-Proto');
        $request->server->set('HTTP_X_FORWARDED_PROTO', 'https');
        $provider->boot();
        $this->assertSame('on', $request->server('HTTPS'));

        $request->server->remove('HTTP_X_FORWARDED_PROTO');
        $request->server->set('HTTPS', 'on');
        $provider->boot();
        $this->assertSame('https://', URL::formatScheme('http'));
    }
}
