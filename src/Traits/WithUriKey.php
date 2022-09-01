<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits;

trait WithUriKey
{
    public function uriKey(): string
    {
        return str(class_basename(get_called_class()))
            ->kebab()
            ->value();
    }
}
