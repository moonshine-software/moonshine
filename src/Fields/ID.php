<?php

namespace Leeto\MoonShine\Fields;


use Illuminate\Database\Eloquent\Model;

class ID extends BaseField
{
    public string $field = 'id';

    public string $label = 'ID';

    protected static string $view = 'input';

    protected static string $type = 'hidden';


    public function indexViewValue(Model $item, bool $container = true): string
    {
        return view('moonshine::shared.badge', [
            'value' => parent::indexViewValue($item, $container),
            'color' => 'pink'
        ]);
    }

}