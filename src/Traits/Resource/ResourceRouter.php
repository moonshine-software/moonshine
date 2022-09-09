<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Resource;

trait ResourceRouter
{
    public function routeNameAlias(): string
    {
        return (string) str(static::class)
            ->classBasename()
            ->replace(['Resource'], '')
            ->plural()
            ->lcfirst();
    }

    public function routeParam(): string
    {
        return (string) str($this->routeNameAlias())->singular();
    }

    public function routeName(?string $action = null): string
    {
        return (string) str(config('moonshine.prefix'))
            ->append('.')
            ->append($this->routeNameAlias())
            ->when($action, fn($str) => $str->append('.')->append($action));
    }
}
