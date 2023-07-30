<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\Traits\Fields\NumberTrait;

class Number extends Text implements DefaultCanBeNumeric
{
    use NumberTrait;

    protected string $type = 'number';

    protected array $attributes = [
        'type',
        'min',
        'max',
        'step',
        'disabled',
        'readonly',
        'required',
    ];

    protected bool $stars = false;

    public function stars(): static
    {
        $this->stars = true;

        return $this;
    }

    public function withStars(): bool
    {
        return $this->stars;
    }

    public function indexViewValue(Model $item, bool $container = true): string
    {
        if ($container && $this->withStars()) {
            return view('moonshine::ui.rating', [
                'value' => $item->{$this->column()},
            ])->render();
        }

        return parent::indexViewValue($item, $container);
    }
}
