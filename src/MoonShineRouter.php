<?php

declare(strict_types=1);

namespace Leeto\MoonShine;

final class MoonShineRouter
{
    public static function to(string $name, array $params = []): string
    {
        return route(
            $name,
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
