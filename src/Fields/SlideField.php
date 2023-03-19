<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Traits\Fields\NumberTrait;
use Leeto\MoonShine\Traits\Fields\SlideTrait;

class SlideField extends Field
{
    use NumberTrait;
    use SlideTrait;

    protected static string $view = 'moonshine::fields.slide';

    protected array $attributes = ['min', 'max', 'step'];

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
            $this->toField => $item->{$this->toField},
        ];
    }

    public function save(Model $item): Model
    {
        if (! $this->canSave) {
            return $item;
        }

        $values = $this->requestValue();

        if ($values === false) {
            return $item;
        }

        $item->{$this->fromField} = $values[$this->fromField] ?? '';
        $item->{$this->toField} = $values[$this->toField] ?? '';

        return $item;
    }
}
