<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Traits\Fields\BooleanTrait;
use MoonShine\Traits\Fields\CheckboxTrait;

class Checkbox extends Field
{
    use CheckboxTrait;
    use BooleanTrait;

    protected static string $view = 'moonshine::fields.checkbox';

    protected string $type = 'checkbox';

    public function indexViewValue(Model $item, bool $container = true): string
    {
        return view('moonshine::ui.boolean', [
            'value' => (bool) $this->formViewValue($item),
        ])->render();
    }
}
