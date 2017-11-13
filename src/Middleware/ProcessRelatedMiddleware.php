<?php

namespace Fico7489\Laravel\UpdatedRelated\Middleware;

use Closure;
use Fico7489\Laravel\UpdatedRelated\Events\ModelChanged;
use Fico7489\Laravel\UpdatedRelated\Services\UpdateRelated;

class ProcessRelatedMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $configurations = \Config::get('laravel-updated-related');

        $events = [];
        foreach ($configurations as $baseModel => $enviroments) {
            foreach ($enviroments as $enviroment) {
                $name          = $enviroment['name'];
                $relatedModels = $enviroment['related'];

                $ids = collect();
                foreach (UpdateRelated::$events as $modelEvent => $idsEvent) {
                    if (array_key_exists($modelEvent, $relatedModels)) {
                        $relation = $relatedModels[$modelEvent];

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
                $events[$baseModel][$name] = $ids;
            }
        }

        foreach($events as $baseModel => $enviroment){
            foreach($enviroment as $enviromentName => $ids){
                foreach($ids as $id){
                    event(new ModelChanged($id, $baseModel, $enviromentName));
                }
            }
        }

        return $response;
    }
}
