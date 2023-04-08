<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Traits\Fields\NumberTrait;
use Leeto\MoonShine\Traits\Fields\SlideTrait;

class SlideField extends Number
{
    use SlideTrait;

    protected static string $view = 'moonshine::fields.slide';

    public function indexViewValue(Model $item, bool $container = true): string
    {
        $from = $item->{$this->fromField};
        $to = $item->{$this->toField};

        if ($this->withStars()) {
            $from = view('moonshine::ui.rating', [
                'value' => $from,
            ])->render();

            $to = view('moonshine::ui.rating', [
                'value' => $to,
            ])->render();
        }

        return "$from - $to";
    }

    public function exportViewValue(Model $item): string
    {
        return "{$item->{$this->fromField}} - {$item->{$this->toField}}";
    }

    public function formViewValue(Model $item): array
    {
        return [
            $this->fromField => $item->{$this->fromField},
            $this->toField => $item->{$this->toField},
        ];
    }

    public function save(Model $item): Model
    {
        $values = $this->requestValue();

        if ($values === false) {
            return $item;
        }

        $item->{$this->fromField} = $values[$this->fromField] ?? '';
        $item->{$this->toField} = $values[$this->toField] ?? '';

        return $item;
    }
}
