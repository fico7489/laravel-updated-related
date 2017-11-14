# Laravel Updated Related

Fires ModelChanged event when base model or any related model is updated, created or deleted.

# Laravel versions

| Laravel Version | Package Tag | Supported | Branch
|-----------------|-------------|-----------|-----------|
| 5.2.x | 1.2 | yes | master
| <5.2 | - | no | -



# How to use

1.Install package with composer
```
composer require fico7489/laravel-updated-related:"1.*"
```
2.Add service provider to config/app.php
```
Fico7489\Laravel\UpdatedRelated\Providers\UpdatedRelatedServiceProvider::class
```
3.Publish configuration 
```
php artisan vendor:publish  --provider="Fico7489\Laravel\UpdatedRelated\Providers\UpdatedRelatedServiceProvider"
```
and after that adjust configuration, see more in below section.

4.Use Fico7489\Laravel\UpdatedRelated\Traits\UpdatedRelatedTrait in your base model or only in a smaller set of models.

5.Use Fico7489\Laravel\UpdatedRelated\Events\ModelChanged event which will be fired when any base model or any related model is updated, created or deleted.

and that's it, enjoy.

# Configuration

Configuration is located at config/laravel-updated-related.php

1.You can create configuration in simple way : 

```
return [
    \App\Models\User::class => [
        [
            \App\Models\DeliveryAddress::class => 'addresses',
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
                \App\Models\DeliveryAddress::class  => 'addresses',
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


# One real example

Configuration :

```
return [
    \App\Models\User::class => [
        [
            \App\Models\DeliveryAddress::class => 'addresses',
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
        return $this->hasMany(DeliveryAddress::class);
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

# Pivot events
If you want to cover pivot events use this package : https://github.com/fico7489/laravel-pivot
```
...
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;

class BaseModel extends Model
{
    use PivotEventTrait;
...
```

# Cover all changes in the database

Do not use code like this one: 
```
User::whereIn('id', $ids)->update(['status' => 'ban']);
```
Bacause in that case laravel does not use a model, it runs query directly without a model. You can change this code to :
```
$users = User::whereIn('id', $ids);
foreach($users as $user){
    $user->update(['status' => 'ban']);
}
```

# When to use this package

* clear cache for model
* update elasticsearch data
* and much more

License
----

MIT


**Free Software, Hell Yeah!**