<?php

namespace Leeto\MoonShine\Fields;


use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Traits\Fields\BooleanTrait;

class SwitchBoolean extends Field
{
    use BooleanTrait;

    protected static string $view = 'switch';

    public function indexViewValue(Model $item, bool $container = true): string
    {
        return view('moonshine::fields.switch', [
            'field' => $this->disabled(),
            'item' => $item
        ]);
    }

}
