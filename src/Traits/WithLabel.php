<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Closure;

trait WithLabel
{
    protected Closure|string $label = '';

    protected bool $translatable = false;

    protected string $translatableKey = '';

    public function label(): string
    {
        $this->label = value($this->label);

        if ($this->translatable) {
            return __(
                str($this->label)->when(
                    $this->translatableKey,
                    fn ($str) => $str->prepend($this->translatableKey . '.')
                )->value()
            );
        }

        return $this->label;
    }

    public function setLabel(Closure|string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function translatable(string $key = ''): static
    {
        $this->translatable = true;
        $this->translatableKey = $key;

        return $this;
    }
}
