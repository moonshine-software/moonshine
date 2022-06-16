<?php

namespace Leeto\MoonShine\Fields;


use Illuminate\Database\Eloquent\Model;

class ID extends Field
{
    public string $field = 'id';

    public string $label = 'ID';

    protected static string $view = 'input';

    protected static string $type = 'hidden';


    public function indexViewValue(Model $item, bool $container = true): string
    {
        return view('moonshine::shared.badge', [
            'value' => parent::indexViewValue($item, $container),
            'color' => 'purple'
        ]);
    }

    public function save(Model $item): Model
    {
        if($this->requestValue()) {
            $item->{$this->field()} = $this->requestValue();
        }

        return $item;
    }
}