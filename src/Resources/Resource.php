<?php

declare(strict_types=1);

namespace MoonShine\Resources;

use MoonShine\Pages\Pages;
use MoonShine\Traits\WithUriKey;

abstract class Resource
{
    use WithUriKey;

    abstract public function pages(): array;

    public function getPages(): Pages
    {
        return Pages::make($this->pages())
            ->setResource($this);
    }
}
