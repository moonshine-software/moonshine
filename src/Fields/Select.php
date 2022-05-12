<?php

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Traits\Fields\SearchableSelectFieldTrait;

class Select extends BaseField
{
    use SearchableSelectFieldTrait;

    protected static string $view = 'select';

    public function indexViewValue(Model $item, bool $container = true): string
    {
        if(isset($this->values()[$item->{$this->field()}])) {
            return $this->values()[$item->{$this->field()}];
        }

        return parent::indexViewValue($item, $container);
    }
}