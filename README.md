# Laravel Updated Related

Update **Elasticsearch** data or **clear cache** when the model or any related model is updated, created or deleted.

## Why to use

In laravel, we have the ability to listen to changes on eloquent models(create, delete, update), and when the model is changed we can do something with model e.g.  we can flush cache for this model.

But sometimes we want to do something with the model when this model is changed or related models are changed.
For example, you have model Seller with related models Order, ZipCodes, Address and OrderItem. If any change on Seller, Address, Order, ZipCode or OrderItem is performed you want to do something with Seller e.g flush cache for seller because cache include related models data: 

```
$seller = Cache::remember('seller_' . $id, $minutes, function () use($id){
    return Seller::with(['orders', 'orders.items', 'addresses', 'zipCodes'])->find($id);
});
```

In above case when Seller model is changed, we have to flush his cache 'seller'.$id but also when his particular related models are updated we have to flush data. Obviously we need some configuration with related models here and this package will help you with this.

You can add this functionality without the package, but there is another huge problem. You can map related models and parent model to know when to flush cache but if you have batch form, e.g. for update all ZipCodes, and if you have 1000 zip codes and hit save, cache will be flushed 1000 times. This is not a problem for a flushing cache but if you have to update Elasticsearch data or perform some other time-consuming operation then it is. 

This package also solves above problem because model changed events are saved to an array and processed after the request when they are filtered to be unique, so if you update 1000 zip codes for the same seller only one event will be dispatched.


## Version Compatibility

The package is available for larvel 5.* versions.


## Install

1.Install package with composer
```
composer require fico7489/laravel-updated-related:"*"
```
2.Add service provider to config/app.php
```
Fico7489\Laravel\UpdatedRelated\Providers\UpdatedRelatedServiceProvider::class
```
3.Publish configuration 
```
php artisan vendor:publish  --provider="Fico7489\Laravel\UpdatedRelated\Providers\UpdatedRelatedServiceProvider"
```
and after that adjust configuration(map model with related models), see more in below section.

4.Use this trait
```
Fico7489\Laravel\UpdatedRelated\Traits\UpdatedRelatedTrait 
```
in your base model.

and that's it, you are ready to go.

## Configuration

Configuration is located at config/laravel-updated-related.php

1.You can create configuration in simple way : 

```
return [
    \App\Models\User::class => [
        [
            \App\Models\Address::class         => 'addresses',
            \App\Models\Order::class           => 'orders',
            \App\Models\OrderItem::class       => 'orders.items',
        ],
    ],
];
```
KEYS are base models, VALUES are arrays with related model => relation

2.Or you can create configuration in detailed way if you need more environments for the same base model. : 

```
return [
    \App\Models\User::class => [
        [
            'name' => 'user-simple',
            'related' => [
                \App\Models\Address::class  => 'addresses',
            ],
        ],
        [
            'name' => 'user-extended',
            'related' => [
                \App\Models\Address::class   => 'addresses',
                \App\Models\OrderItem::class => 'orders.items',
            ],
        ],
    ]
];
```

KEYS are base models, VALUES are arrays with names (environment name) and related configuration (arrays with related model => relation). In "simple way" environment will be 'default'.

After you set a configuration just listened to the Model Change event that will be dispatched when any model or its related model (defined in configuration) is changed (updated, deleted, created).

## One real example

Configuration :

```
return [
    \App\Models\User::class => [
        [
            \App\Models\Address::class => 'addresses',
        ],
    ],
];
```

Models :

```
...
class User extends BaseModel
{
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
...
```

```
...
class Address extends BaseModel
{
....
```

Event service provider :
```
...
protected $listen = [
    ModelChanged::class => [
        TestListener::class,
    ],
...
```

Listener :
```
use Fico7489\Laravel\UpdatedRelated\Events\ModelChanged;

class TestListener
{
    public function handle(ModelChanged $event)
    {
        echo 'id=' . $event->getId();
        echo 'model=' . $event->getModel();
        echo 'environment=' . $event->getName();
    }
}
```

```
User::find(1)->touch();
Address::find(2)->touch(); //let's assume that user_id is 10 here
```

With above code you will se this output:
```
id=1
model=App\Models\User
environment=default

id=10
model=App\Models\User
environment=default
```
If you create, delete or update User or Address model ModelChanged will be dispatched.

## Cover all changes in the database

Do not use code like this one: 
```
User::whereIn('id', $ids)->update(['status' => 'ban']);
```
Because in that case, laravel does not use a model, it runs query directly without a model. To cover above changes you can change this code to :
```
$users = User::whereIn('id', $ids);
foreach($users as $user){
    $user->update(['status' => 'ban']);
}
```

### Pivot events
If you want to cover pivot events use this package : https://github.com/fico7489/laravel-pivot


## When to use this package
* clear cache for model
* update elasticsearch data
* and much more

License
----

MIT


**Free Software, Hell Yeah!**