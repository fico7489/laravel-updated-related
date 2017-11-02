<?php

namespace Fico7489\Laravel\UpdatedRelated\Middleware;

use Closure;
use Fico7489\Laravel\UpdatedRelated\Services\UpdateRelated;

class ProcessRelatedMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $config = \Config::get('laravel-updated-related');

        $ids = collect();
        foreach (UpdateRelated::$events as $modelEvent => $idsEvent) {
            foreach ($config as $modelRoot => $configurationModel) {
                if (array_key_exists($modelEvent, $configurationModel)) {
                    $relations = $configurationModel[$modelEvent];

                    $idsTmp = $modelRoot::whereHas($relations, function ($query) use ($idsEvent) {
                        return $query->whereIn('id', $idsEvent);
                    })->get()->pluck('id');

                    $ids = $ids->merge($idsTmp)->unique();
                }

                if ($modelEvent == $modelRoot) {
                    $ids = $ids->merge($idsEvent);
                }
            }
        }
        $ids = $ids->unique()->sort();

        return $response;
    }
}
