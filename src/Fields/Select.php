<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Traits\Fields\CanBeMultiple;
use MoonShine\Traits\Fields\Searchable;
use MoonShine\Traits\Fields\SelectTrait;

class Select extends Field
{
    use CanBeMultiple;
    use Searchable;
    use SelectTrait;

    protected static string $view = 'moonshine::fields.select';

    public function indexViewValue(Model $item, bool $container = true): string
    {
        $value = $item->{$this->field()};

        if ($this->isMultiple()) {
            if (is_string($value)) {
                $value = json_decode($value, true);
            }

            return collect($value)->map(function ($v) {
                return view('moonshine::ui.badge', [
                    'color' => 'purple',
                    'value' => $this->values()[$v] ?? false,
                ])->render();
            })->implode(',');
        }

        return (string)(
            $this->values()[$item->{$this->field()}]
            ?? parent::indexViewValue($item, $container)
        );
    }
}
