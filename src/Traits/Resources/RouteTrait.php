<?php

namespace Leeto\MoonShine\Traits\Resources;

trait RouteTrait
{
    public function route(string $action, int $id = null, array $query = []): string
    {
        $route = str(request()->route()->getName())->beforeLast('.');

        if($id) {
            $parameter = $route->afterLast(config('moonshine.route.prefix') . '.')->singular();

            return route(
                "$route.$action",
                array_merge([(string) $parameter => $id], $query)
            );
        } else {
            return route("$route.$action", $query);
        }
    }
}