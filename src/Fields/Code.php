<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

class Code extends Field
{
    protected static string $component = 'Code';

    public string $language = 'php';

    public bool $lineNumbers = false;

    public function language(string $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function lineNumbers(): static
    {
        $this->lineNumbers = true;

        return $this;
    }
}
