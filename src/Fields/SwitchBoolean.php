<?php

namespace Leeto\MoonShine\Fields;


use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Traits\Fields\BooleanFieldTrait;

class SwitchBoolean extends BaseField
{
    use BooleanFieldTrait;

    protected static string $view = 'switch';

    public function indexViewValue(Model $item, bool $container = true): string
    {

        return view('moonshine::fields.switch', [
            'field' => $this->disabled(),
            'item' => $item
        ]);
    }

}