<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class ID extends Field
{
    public string $field = 'id';

    public string $label = 'ID';

    protected static string $view = 'moonshine::fields.input';

    protected static string $type = 'hidden';


    public function indexViewValue(Model $item, bool $container = true): View
    {
        return view('moonshine::ui.badge', [
            'value' => parent::indexViewValue($item, $container),
            'color' => 'purple',
        ]);
    }

    public function exportViewValue(Model $item): mixed
    {
        return $item->{$this->field()};
    }

    public function save(Model $item): Model
    {
        if ($this->requestValue()) {
            $item->{$this->field()} = $this->requestValue();
        }

        return $item;
    }
}
