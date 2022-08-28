<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Contracts\Fields\Fileable;
use Leeto\MoonShine\Traits\Fields\CanBeMultiple;
use Leeto\MoonShine\Traits\Fields\FileTrait;
use Leeto\MoonShine\Traits\Fields\Removable;
use Leeto\MoonShine\Contracts\Fields\Removable as RemovableContact;

class File extends Field implements Fileable, RemovableContact
{
    use CanBeMultiple;
    use FileTrait;
    use Removable;

    protected static string $component = 'FileField';

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
