<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Views;

use Illuminate\Support\Collection;

final class Views extends Collection
{
    /**
     * @param  string  $uriKey
     * @param  string|null  $default
     * @return ?string
     */
    public function findByUriKey(string $uriKey, ?string $default = null): ?string
    {
        return $this->first(function ($class) use ($uriKey) {
            return str($class)->classBasename()
                ->contains(str($uriKey)->studly());
        }, $default);
    }
}
