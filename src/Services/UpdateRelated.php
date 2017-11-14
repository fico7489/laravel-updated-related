<?php

namespace Fico7489\Laravel\UpdatedRelated\Services;
use Fico7489\Laravel\UpdatedRelated\Events\ModelChanged;

class UpdateRelated
{
    public static $events = [];
    public static $eventsProcessed = [];
    public static $cofiguration = [];
    
    public static function processEvents(){
        $configurations = \Config::get('laravel-updated-related');

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

        foreach ($configurations as $baseModel => $environments) {
            foreach ($environments as $environment) {
                $name    = $environment['name'];
                $related = $environment['related'];

                $ids = collect();
                foreach (self::$events as $modelEvent => $idsEvent) {
                    if (array_key_exists($modelEvent, $related)) {
                        $relation = $related[$modelEvent];
                        
                        $idsTmp = $baseModel::whereHas($relation, function ($query) use ($idsEvent) {
                            return $query->whereIn('id', $idsEvent);
                        })->get()->pluck('id');

                        $ids = $ids->merge($idsTmp);
                    }

                    if ($modelEvent == $baseModel) {
                        $ids = $ids->merge($idsEvent);
                    }
                }
                $ids = $ids->unique()->sort();
                
                if(empty(self::$eventsProcessed[$baseModel][$name])){
                    self::$eventsProcessed[$baseModel][$name] = [];
                }
                self::$eventsProcessed[$baseModel][$name] += $ids->toArray();
            }
        }
        self::$events = [];
    }
    
    public static function fireEvents(){
        self::processEvents();
        
        foreach(self::$eventsProcessed as $baseModel => $environments){
            foreach ($environments as $environmentName => $ids) {
                foreach($ids as $id){
                    event(new ModelChanged($id, $baseModel, $environmentName));
                }
            }
        }
        
        self::$eventsProcessed = [];
    }
}
