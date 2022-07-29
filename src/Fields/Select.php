<?php

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Traits\Fields\CanBeMultiple;
use Leeto\MoonShine\Traits\Fields\Searchable;
use Leeto\MoonShine\Traits\Fields\SelectTrait;

class Select extends Field
{
    use Searchable, CanBeMultiple, SelectTrait;

    protected static string $view = 'select';

    public function indexViewValue(Model $item, bool $container = true): string
    {
        if (isset($this->values()[$item->{$this->field()}])) {
            return $this->values()[$item->{$this->field()}];
        }

        return parent::indexViewValue($item, $container);
    }
}
