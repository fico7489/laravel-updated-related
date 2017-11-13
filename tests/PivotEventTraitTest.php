<?php

namespace Fico7489\Laravel\UpdatedRelated\Tests;

use Fico7489\Laravel\UpdatedRelated\Tests\Models\User;
use Fico7489\Laravel\UpdatedRelated\Tests\Models\Order;
use Fico7489\Laravel\UpdatedRelated\Tests\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;

class PivotEventTraitTest extends TestCase
{
    static $events = [];

    public function setUp()
    {
        parent::setUp();

        $user = User::create();
        $user2 = User::create();

        $order = Order::create(['user_id' => $user->id]);
        $order2 = Order::create(['user_id' => $user->id]);
        $order3 = Order::create(['user_id' => $user2->id]);
        
        $orderItem = OrderItem::create(['order_id' => $order->id]);
        
        $orderItem2 = OrderItem::create(['order_id' => $order2->id]);
        $orderItem3 = OrderItem::create(['order_id' => $order2->id]);
        
        $orderItem4 = OrderItem::create(['order_id' => $order3->id]);
        $orderItem5 = OrderItem::create(['order_id' => $order3->id]);
        $orderItem6 = OrderItem::create(['order_id' => $order3->id]);
    }

    public function test_attach_events()
    {
        $this->assertTrue(true);
    }
}