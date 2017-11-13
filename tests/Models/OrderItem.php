<?php

namespace Fico7489\Laravel\UpdatedRelated\Tests\Models;

class OrderItem extends BaseModel
{
    protected $table = 'order_items';
    
    protected $fillable = ['order_id'];
}
