<?php

namespace Fico7489\Laravel\UpdatedRelated\Tests;

use Fico7489\Laravel\UpdatedRelated\Tests\Models\User;
use Fico7489\Laravel\UpdatedRelated\Tests\Models\Order;
use Fico7489\Laravel\UpdatedRelated\Tests\Models\OrderItem;
use Fico7489\Laravel\UpdatedRelated\Tests\TestListener;
use Fico7489\Laravel\UpdatedRelated\Services\UpdateRelated;
use Illuminate\Database\Eloquent\Model;

class LaravelUpdatedRelatedTest extends TestCase
{
    static $events = [];

    public function setUp()
    {
        parent::setUp();

        $user = User::create();
        $user2 = User::create();
        $user3 = User::create();

        $order = Order::create(['user_id' => $user->id]);
        $order2 = Order::create(['user_id' => $user->id]);
        $order3 = Order::create(['user_id' => $user2->id]);
        $order4 = Order::create(['user_id' => $user3->id]);
        
        $orderItem = OrderItem::create(['order_id' => $order->id]);
        
        $orderItem2 = OrderItem::create(['order_id' => $order2->id]);
        $orderItem3 = OrderItem::create(['order_id' => $order2->id]);
        
        $orderItem4 = OrderItem::create(['order_id' => $order3->id]);
        $orderItem5 = OrderItem::create(['order_id' => $order3->id]);
        $orderItem6 = OrderItem::create(['order_id' => $order3->id]);
        $orderItem7 = OrderItem::create(['order_id' => $order4->id]);
    }
    
    private function startListening(){
        UpdateRelated::$events = [];
        TestListener::$events  = [];
    }

    public function test_create()
    {
        $this->startListening();
        User::create();
        UpdateRelated::fireEvents();
        
        $this->assertEquals(2, count(TestListener::$events));
        $this->assertEquals(4, TestListener::$events[0]->getId());
        $this->assertEquals(\Fico7489\Laravel\UpdatedRelated\Tests\Models\User::class, TestListener::$events[0]->getModel());
        $this->assertEquals('default', TestListener::$events[0]->getName());
        $this->assertEquals('special', TestListener::$events[1]->getName());
    }
    
    public function test_update()
    {
        $this->startListening();
        User::find(1)->update(['email' => 'test@test.com']);
        UpdateRelated::fireEvents();
        $this->assertEquals(2, count(TestListener::$events));
    }
    
    public function test_delete()
    {
        $this->startListening();
        User::find(1)->forceDelete();
        UpdateRelated::fireEvents();
        $this->assertEquals(2, count(TestListener::$events));
    }
    
    
    public function test_create_related()
    {
        $this->startListening();
        Order::create(['user_id' => 1]);
        UpdateRelated::fireEvents();
        $this->assertEquals(1, count(TestListener::$events));
    }
    
    public function test_update_related()
    {
        $this->startListening();
        Order::find(1)->update(['number' => 1]);
        UpdateRelated::fireEvents();
        $this->assertEquals(1, count(TestListener::$events));
    }
    
    public function test_delete_related()
    {
        $this->startListening();
        Order::find(1)->forceDelete();
        UpdateRelated::fireEvents();
        $this->assertEquals(1, count(TestListener::$events));
    }
    
    
    public function test_create_related_second()
    {
        $this->startListening();
        OrderItem::create(['order_id' => 1]);
        UpdateRelated::fireEvents();
        $this->assertEquals(1, count(TestListener::$events));
    }
    
    public function test_update_related_second()
    {
        $this->startListening();
        OrderItem::find(1)->update(['name' => 'item 2']);
        UpdateRelated::fireEvents();
        $this->assertEquals(1, count(TestListener::$events));
    }
    
    public function test_delete_related_second()
    {
        $this->startListening();
        OrderItem::find(1)->forceDelete();
        UpdateRelated::fireEvents();
        $this->assertEquals(1, count(TestListener::$events));
    }
    
    
    public function test_create_id()
    {
        $this->startListening();
        OrderItem::create(['order_id' => 3]);
        UpdateRelated::fireEvents();
        $this->assertEquals(2, TestListener::$events[0]->getId());
    }
    
    public function test_update_id()
    {
        $this->startListening();
        OrderItem::find(6)->update(['name' => 'item 2']);
        UpdateRelated::fireEvents();
        $this->assertEquals(2, TestListener::$events[0]->getId());
    }
    
    public function test_delete_id()
    {
        $this->startListening();
        OrderItem::find(6)->forceDelete();
        UpdateRelated::fireEvents();
        $this->assertEquals(2, TestListener::$events[0]->getId());
    }
    
    
    public function test_three_events()
    {
        $this->startListening();
        User::find(1)->update(['email' => 'test@test.com']);
        Order::find(3)->update(['number' => 1]);
        OrderItem::find(7)->update(['name' => 'item 2']);
        UpdateRelated::fireEvents();
        $this->assertEquals(4, count(TestListener::$events));
    }
    
    public function test_two_with_delete()
    {
        $this->startListening();
        User::find(1)->update(['email' => 'test@test.com']);
        Order::find(3)->forceDelete();
        UpdateRelated::fireEvents();
        $this->assertEquals(3, count(TestListener::$events));
    }
}