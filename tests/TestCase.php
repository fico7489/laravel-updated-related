<?php

namespace Fico7489\Laravel\UpdatedRelated\Tests;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('laravel-updated-related', [
            \Fico7489\Laravel\UpdatedRelated\Tests\Models\User::class => [
                [
                    \Fico7489\Laravel\UpdatedRelated\Tests\Models\Order::class => 'orders',
                    \Fico7489\Laravel\UpdatedRelated\Tests\Models\OrderItem::class => 'orders.items',
                    \Fico7489\Laravel\UpdatedRelated\Tests\Models\Address::class => 'addresses',
                ],
                [
                    'name' => 'special',
                    'related' => [
                        \Fico7489\Laravel\UpdatedRelated\Tests\Models\Address::class => 'addresses',
                    ],
                ],
            ],
            \Fico7489\Laravel\UpdatedRelated\Tests\Models\Seller::class => [
                [
                    \Fico7489\Laravel\UpdatedRelated\Tests\Models\Address::class => 'addresses',
                ],
            ],
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
            EventServiceProvider::class,
        ];
    }
}
