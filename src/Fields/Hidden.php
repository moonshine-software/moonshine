<?php

declare(strict_types=1);

namespace MoonShine\Fields;

class Hidden extends Text
{
    protected string $type = 'hidden';

    protected bool $showOnIndex = false;

    protected bool $showOnDetail = false;

    protected bool $showOnExport = false;
}
