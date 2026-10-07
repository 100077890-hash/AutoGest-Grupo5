<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Inyectar la interfaz de autenticación con estado usando el guard web nativo.
        $this->app->bind(StatefulGuard::class, fn ($app) => $app['auth']->guard('web'));
    }
}
