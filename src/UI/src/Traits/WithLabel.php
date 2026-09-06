<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use Closure;
use Illuminate\Support\Str;

trait WithLabel
{
    /** @var (Closure(static): string)|string */
    protected Closure|string $label = '';

    protected bool $translatable = false;

    protected string $translatableKey = '';

    public function hasLabel(): bool
    {
        return $this->label !== '';
    }

    public function getLabel(): string
    {
        $this->label = value($this->label, $this);

        if ($this->translatable) {
            return $this->getCore()->getTranslator()->getString(
                Str::of($this->label)->when(
                    $this->translatableKey,
                    fn ($str) => $str->prepend($this->translatableKey . '.')
                )->value()
            );
        }

        return $this->label;
    }

    /** @param (Closure(static): string)|string $label */
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
