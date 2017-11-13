<?php

namespace Fico7489\Laravel\UpdatedRelated\Tests\Models;

class User extends BaseModel
{
    protected $table = 'users';

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}
