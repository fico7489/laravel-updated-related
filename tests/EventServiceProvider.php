<?php

namespace Fico7489\Laravel\UpdatedRelated\Tests;

use Fico7489\Laravel\UpdatedRelated\Events\ModelChanged;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ModelChanged::class => [
            TestListener::class,
        ],
    ];

    public function register()
    {
    }
}
