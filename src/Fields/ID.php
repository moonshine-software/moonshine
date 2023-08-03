<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;

class ID extends Text
{
    protected string $field = 'id';

    protected string $label = 'ID';

    protected string $type = 'hidden';

    protected function resolvePreview(): string
    {
        return view('moonshine::ui.badge', [
            'value' => $this->toValue(),
            'color' => 'purple',
        ])->render();
    }
}
