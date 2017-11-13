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

        $events = [];
        foreach ($configurations as $baseModel => $environments) {
            foreach ($environments as $environment) {
                $name          = $environment['name'];
                $relatedModels = $environment['related'];

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

        foreach($events as $baseModel => $environment){
            foreach($environment as $environmentName => $ids){
                foreach($ids as $id){
                    event(new ModelChanged($id, $baseModel, $environmentName));
                }
            }
        }

        return $response;
    }
}
