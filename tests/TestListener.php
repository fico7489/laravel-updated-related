<?php

namespace Fico7489\Laravel\UpdatedRelated\Tests;

use Fico7489\Laravel\UpdatedRelated\Events\ModelChanged;

class TestListener
{
    public static $events = [];

    public function handle(ModelChanged $event)
    {
        self::$events[] = $event;
    }
}
