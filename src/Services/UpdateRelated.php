<?php

namespace Fico7489\Laravel\UpdatedRelated\Services;

use Fico7489\Laravel\UpdatedRelated\Events\ModelChanged;
use Config;

class UpdateRelated
{
    public static $events = [];
    public static $eventsProcessed = [];
    public static $cofiguration = [];

    public static function processEvents()
    {
        $configurations = Config::get('laravel-updated-related');

        $configurations = self::prepareConfiguration($configurations);

        // loop all configurations, ie the environments
        foreach ($configurations as $baseModel => $environments) {
            foreach ($environments as $environment) {
                // environments name e.g. 'default'
                $name = $environment['name'];

                // mapping models to relations e.g. \App\Models\Address::class => 'addresses'
                $modelRelationMapping = $environment['related'];

                $modelIdsChanged = collect();

                // in self::$events we have all changed models with ids, e.g. [App\Models\Address => [1, 2, 3]]
                foreach (self::$events as $eventModel => $eventIds) {
                    // check if we have changed model in package configuration
                    if (array_key_exists($eventModel, $modelRelationMapping)) {
                        // get relation so we can reach root model from changed model
                        $relation = $modelRelationMapping[$eventModel];

                        $idsTmp = $baseModel::whereHas($relation, function ($query) use ($eventIds) {
                            return $query->whereIn('id', $eventIds);
                        })->get()->pluck('id');

                        // now we have all changed model ids
                        $modelIdsChanged = $modelIdsChanged->merge($idsTmp);
                    }

                    // if the changed model in the event is root model just get his ids
                    if ($eventModel == $baseModel) {
                        $modelIdsChanged = $modelIdsChanged->merge($eventIds);
                    }
                }

                // make changed ids unique and sort
                $modelIdsChanged = $modelIdsChanged->unique()->sort();

                // finally store all changed ids and models in $eventsProcessed
                self::$eventsProcessed[$baseModel][$name] = !empty(self::$eventsProcessed[$baseModel][$name]) ? self::$eventsProcessed[$baseModel][$name] : [];
                self::$eventsProcessed[$baseModel][$name] += $modelIdsChanged->toArray();
            }
        }

        // clear events that are already processed
        self::$events = [];
    }

    public static function fireEvents()
    {
        self::processEvents();

        // fire ModelChanged event for all change models and also for each environment
        foreach (self::$eventsProcessed as $baseModel => $environments) {
            foreach ($environments as $environmentName => $ids) {
                foreach ($ids as $id) {
                    event(new ModelChanged($id, $baseModel, $environmentName));
                }
            }
        }

        self::$eventsProcessed = [];
    }

    private static function prepareConfiguration($configurations)
    {
        // all configuration should have a name, but if there is no name we should give 'default' name so we would have all configurations uniform
        foreach ($configurations as $baseModel => $environments) {
            foreach ($environments as $k => $environment) {
                if (!array_key_exists('name', $environment)) {
                    $configurations[$baseModel][$k] = [
                        'name' => 'default',
                        'related' => $environment,
                    ];
                }
            }
        }

        return $configurations;
    }
}
