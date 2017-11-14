<?php

namespace Fico7489\Laravel\UpdatedRelated\Tests\Models;

class User extends BaseModel
{
    protected $table = 'users';
    
    protected $fillable = ['email'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
