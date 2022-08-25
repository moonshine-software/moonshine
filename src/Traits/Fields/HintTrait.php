<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

trait HintTrait
{
    protected string $hint = '';

    /**
     * Define a field description(hint), which will be displayed on create/edit page
     *
     * @param  string  $hint
     * @return $this
     */
    public function hint(string $hint): static
    {
        $this->hint = $hint;

        return $this;
    }

    public function getHint(): string
    {
        return $this->hint;
    }
}
