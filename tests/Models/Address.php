<?php

namespace Fico7489\Laravel\UpdatedRelated\Tests\Models;

class Address extends BaseModel
{
    protected $table = 'addresses';

    protected $fillable = ['seller_id', 'name'];
}
