<?php

declare(strict_types=1);

namespace Leeto\MoonShine\FormComponents;

use Leeto\MoonShine\Contracts\HtmlViewable;
use Leeto\MoonShine\Traits\Fields\HasCanSee;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithView;

abstract class FormComponent implements HtmlViewable
{
    use Makeable;
    use HasCanSee;
    use WithView;

    final public function __construct(
        protected string $label
    ) {
    }

    public function id(string $index = null): string
    {
        return (string) str($this->label())->slug();
    }

    public function name(string $index = null): string
    {
        return $this->id($index);
    }

    public function label(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }
}
