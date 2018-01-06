<?php

namespace Fico7489\Laravel\UpdatedRelated\Traits;

use Fico7489\Laravel\UpdatedRelated\Services\UpdateRelated;

trait UpdatedRelatedTrait
{
    public static function bootUpdatedRelatedTrait()
    {
        static::created(function ($model) {
            // we must fill events after model is created, because before model does not have id
            self::fillEvents($model);
        });

        static::updating(function ($model) {
            self::fillEvents($model);
        });

        static::deleting(function ($model) {
            // we must fill events before model is deleted
            // also for deleted events we must fill events immediately, because we are doing queries with this model in fillEvents and this is problem if model is hard deleted
            self::fillEvents($model, true);
        });
    }

    private static function fillEvents($model, $flush = false)
    {
        $modelId = $model->id;
        $modelType = get_class($model);

        UpdateRelated::$events[$modelType][] = $modelId;

        if ($flush) {
            UpdateRelated::processEvents();
        }
    }
}
