<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Traits\Fields\NumberTrait;

class Number extends Field
{
    use NumberTrait;

    protected static string $view = 'moonshine::fields.input';

    protected static string $type = 'number';

    protected array $attributes = ['min', 'max', 'step'];

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

    public function indexViewValue(Model $item, bool $container = true): mixed
    {
        if ($this->withStars()) {
            return view('moonshine::ui.rating', [
                'value' => $item->{$this->field()},
            ]);
        } else {
            return parent::indexViewValue($item, $container);
        }
    }

    public function exportViewValue(Model $item): mixed
    {
        return $item->{$this->field()};
    }
}
