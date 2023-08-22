<?php

declare(strict_types=1);

namespace MoonShine\Fields;

class Image extends File
{
    protected string $view = 'moonshine::fields.image';

    protected string $itemView = 'moonshine::ui.image';
}
