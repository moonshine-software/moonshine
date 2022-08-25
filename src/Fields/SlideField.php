<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Leeto\MoonShine\Traits\Fields\NumberTrait;
use Leeto\MoonShine\Traits\Fields\SlideTrait;

class SlideField extends Field
{
    use NumberTrait;
    use SlideTrait;

    protected array $attributes = ['min', 'max', 'step'];

    protected static string $component = 'SlideField';

    public function resolveFill($values): static
    {
        if (isset($values[$this->from()], $values[$this->to()])) {
            $this->setValue([$values[$this->from()], $values[$this->to()]]);
        }

        return $this;
    }
}
