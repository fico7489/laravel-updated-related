<?php

namespace Fico7489\Laravel\UpdatedRelated\Tests\Models;

use Fico7489\Laravel\UpdatedRelated\Traits\UpdatedRelatedTrait;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use UpdatedRelatedTrait;
}
