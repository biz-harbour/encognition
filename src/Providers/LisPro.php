<?php

namespace Licon\Lis\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Licon\Lis\Http\Middleware\LisMid;

final class LisPro extends ServiceProvider
{
    public function boot(Router $router): void
    {
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', LisMid::class);

        // $this->app['router']->aliasMiddleware('web', LisMid::class);
        $this->app->register(LisPro::class);
    }

    public function register(): void
    {

    }
}
