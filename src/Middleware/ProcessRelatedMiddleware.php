<?php

namespace Fico7489\Laravel\UpdatedRelated\Middleware;

use Closure;
use Fico7489\Laravel\UpdatedRelated\Services\UpdateRelated;

class ProcessRelatedMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // fire event after the request is finished
        UpdateRelated::fireEvents();

        return $response;
    }
}
