<?php

namespace Fico7489\Laravel\UpdatedRelated\Tests;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Fico7489\Laravel\UpdatedRelated\Events\ModelChanged;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ModelChanged::class => [
            TestListener::class,
        ],
    ];

    public function register()
    {
        //
    }

}
