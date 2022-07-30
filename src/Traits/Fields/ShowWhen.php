<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

trait ShowWhen
{
    public bool $showWhenState = false;

    public string $showWhenField;

    public string $showWhenValue;

    public function showWhen(string $field_name, string $item_value): static
    {
        $this->showWhenState = true;
        $this->showWhenField = $field_name;
        $this->showWhenValue = $item_value;

        return $this;
    }
}
