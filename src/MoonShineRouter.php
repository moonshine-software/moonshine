<?php

declare(strict_types=1);

namespace MoonShine;

final class MoonShineRouter
{
    public static function to(string $name, array $params = []): string
    {
        return route(
            str($name)
                ->remove('moonshine.')
                ->prepend('moonshine.')
                ->value(),
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
