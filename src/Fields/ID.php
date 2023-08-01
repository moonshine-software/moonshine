<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;

class ID extends Text
{
    protected string $field = 'id';

    protected string $label = 'ID';

    protected string $type = 'hidden';

    public function resolvePreview(): string
    {
        return view('moonshine::ui.badge', [
            'value' => $this->getValue(),
            'color' => 'purple',
        ])->render();
    }

    public function save(Model $item): Model
    {
        if ($this->requestValue()) {
            $item->{$this->column()} = $this->requestValue();
        }

        return $item;
    }
}
