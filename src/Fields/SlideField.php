<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Traits\Fields\NumberTrait;
use Leeto\MoonShine\Traits\Fields\SlideTrait;

class SlideField extends Field
{
    use NumberTrait, SlideTrait;

    protected array $attributes = ['min', 'max', 'step'];

    protected static string $view = 'moonshine::fields.slide';

    public function save(Model $item): Model
    {
        $values = $this->requestValue();

        if ($values === false) {
            return $item;
        }

        $item[$this->fromField] = $values[$this->fromField] ?? '';
        $item[$this->toField] = $values[$this->toField] ?? '';

        return $item;
    }
}
