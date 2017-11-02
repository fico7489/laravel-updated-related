<?php

namespace Fico7489\Laravel\UpdatedRelated\Traits;

use Fico7489\Laravel\UpdatedRelated\Services\UpdateRelated;

trait UpdatedRelatedTrait
{
    public static function bootUpdatedRelatedTrait()
    {
        static::created(function ($model) {
            self::fillEvents($model);
        });

        static::updated(function ($model) {
            self::fillEvents($model);
        });

        static::deleted(function ($model) {
            self::fillEvents($model);
        });
    }

    private static function fillEvents($model){
        UpdateRelated::$events[get_class($model)][] = $model->id;
    }
}
