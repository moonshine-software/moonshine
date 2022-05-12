<?php

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Traits\Fields\NumberFieldTrait;

class Number extends BaseField
{
    use NumberFieldTrait;

    protected static string $view = 'input';

    protected static string $type = 'number';

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
        if($this->withStars()) {
            return view('moonshine::shared.stars', [
                'value' => $item->{$this->field()}
            ]);
        } else {
            return parent::indexViewValue($item, $container);
        }
    }
}