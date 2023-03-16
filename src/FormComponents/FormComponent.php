<?php

declare(strict_types=1);

namespace Leeto\MoonShine\FormComponents;

use Leeto\MoonShine\Contracts\HtmlViewable;
use Leeto\MoonShine\Traits\Fields\HasCanSee;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithLabel;
use Leeto\MoonShine\Traits\WithView;

abstract class FormComponent implements HtmlViewable
{
    use Makeable;
    use HasCanSee;
    use WithView;
    use WithLabel;

    final public function __construct(
        string $label
    ) {
        $this->setLabel($label);
    }

    public function id(string $index = null): string
    {
        return (string) str($this->label())->slug('_');
    }

    public function name(string $index = null): string
    {
        return $this->id($index);
    }
}
