<?php

namespace Fico7489\Laravel\UpdatedRelated\Providers;

use Fico7489\Laravel\UpdatedRelated\Middleware\ProcessRelatedMiddleware;
use Illuminate\Support\ServiceProvider;

class UpdatedRelatedServiceProvider extends ServiceProvider
{
    public function register()
    {
        app(\Illuminate\Contracts\Http\Kernel::class)->prependMiddleware(ProcessRelatedMiddleware::class);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/laravel-updated-related.php' => config_path('laravel-updated-related.php', 'config'),
        ]);
    }
}
