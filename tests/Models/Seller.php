<?php

namespace Fico7489\Laravel\UpdatedRelated\Tests\Models;

class Seller extends BaseModel
{
    protected $table = 'sellers';
    
    protected $fillable = ['name'];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
