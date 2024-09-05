<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use Closure;

trait WithLabel
{
    protected Closure|string $label = '';

    protected bool $translatable = false;

    protected string $translatableKey = '';

    public function getLabel(): string
    {
        $this->label = value($this->label, $this);

        if ($this->translatable) {
            return $this->getCore()->getTranslator()->get(
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
