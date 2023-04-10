<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;

class ID extends Text
{
    protected string $field = 'id';

    protected string $label = 'ID';

    protected string $type = 'hidden';

    public function indexViewValue(Model $item, bool $container = true): string
    {
        return view('moonshine::ui.badge', [
            'value' => parent::indexViewValue($item, $container),
            'color' => 'purple',
        ])->render();
    }

    public function exportViewValue(Model $item): string
    {
        return (string) $item->{$this->field()};
    }

    public function save(Model $item): Model
    {
        if ($this->requestValue()) {
            $item->{$this->field()} = $this->requestValue();
        }

        return $item;
    }
}
