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

        static::updating(function ($model) {
            self::fillEvents($model);
        });

        static::deleting(function ($model) {
            self::fillEvents($model, true);
        });
    }

    private static function fillEvents($model, $flush = false)
    {
        UpdateRelated::$events[get_class($model)][] = $model->id;
        if ($flush) {
            UpdateRelated::processEvents();
        }
    }
}
