<?php

declare(strict_types=1);

namespace Leeto\MoonShine;

class MoonShineRouter
{
    public static function to(string $name, array $params = []): string
    {
        return route(
            config('moonshine.prefix').".$name",
            $params
        );
    }

    public static function uriKey(string $class): string
    {
        return str(class_basename($class))
            ->kebab()
            ->value();
    }
}
