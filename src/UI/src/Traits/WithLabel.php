<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use Closure;
use Illuminate\Support\Str;

trait WithLabel
{
    protected Closure|string $label = '';

    protected bool $unescapeLabel = false;

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
            return $this->getCore()->getTranslator()->get(
                Str::of($this->label)->when(
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

    public function unescapeLabel(bool $unescape = true): static
    {
        $this->unescapeLabel = $unescape;

        return $this;
    }

    public function isUnescapeLabel(): bool
    {
        return $this->unescapeLabel;
    }

    public function translatable(string $key = ''): static
    {
        $this->translatable = true;
        $this->translatableKey = $key;

        return $this;
    }
}
