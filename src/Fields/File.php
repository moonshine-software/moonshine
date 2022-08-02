<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Contracts\Fields\Fileable;
use Leeto\MoonShine\Traits\Fields\CanBeMultiple;
use Leeto\MoonShine\Traits\Fields\FileTrait;

class File extends Field implements Fileable
{
    use FileTrait, CanBeMultiple;

    protected static string $view = 'moonshine::fields.file';

    protected static string $type = 'file';

    protected string $accept = '*/*';

    protected array $attributes = [
        'accept'
    ];

    public function accept(string $value): static
    {
        $this->accept = $value;

        return $this;
    }
}
