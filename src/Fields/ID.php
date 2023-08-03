<?php

declare(strict_types=1);

namespace MoonShine\Fields;

class ID extends Hidden
{
    protected string $field = 'id';

    protected string $label = 'ID';

    protected function resolvePreview(): string
    {
        return view('moonshine::ui.badge', [
            'value' => $this->toValue(),
            'color' => 'purple',
        ])->render();
    }
}
