<?php

namespace Leeto\MoonShine\Fields;


use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Traits\Fields\NumberFieldTrait;

class SlideField extends BaseField
{
    use NumberFieldTrait;

    protected static string $view = 'slide';

    public function indexViewValue(Model $item, bool $container = true): string
    {
        return "{$item->{$this->fromField}} - {$item->{$this->toField}}";
    }

    public function exportViewValue(Model $item): string
    {
        return "{$item->{$this->fromField}} - {$item->{$this->toField}}";
    }

    public function formViewValue(Model $item): array
    {
        return [
            $this->fromField => $item->{$this->fromField},
            $this->toField => $item->{$this->toField}
        ];
    }

    public function save(Model $item): Model
    {
        $values = $this->requestValue();

        if($values === false) {
            return $item;
        }

        $item->{$this->fromField} = $values[$this->fromField] ?? '';
        $item->{$this->toField} = $values[$this->toField] ?? '';

        return $item;
    }
}