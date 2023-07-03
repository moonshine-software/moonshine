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
        if (!$container) {
            return parent::indexViewValue($item, $container);
        }

        return view('moonshine::ui.badge', [
            'value' => parent::indexViewValue($item, $container),
            'color' => 'purple',
        ])->render();
    }

    public function save(Model $item): Model
    {
        if ($this->requestValue()) {
            $item->{$this->field()} = $this->requestValue();
        }

        return $item;
    }
}
