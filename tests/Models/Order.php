<?php

namespace Fico7489\Laravel\UpdatedRelated\Tests\Models;

class Order extends BaseModel
{
    protected $table = 'orders';

    protected $fillable = ['user_id', 'number'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
