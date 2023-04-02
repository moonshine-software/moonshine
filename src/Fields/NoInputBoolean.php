<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Helpers\Condition;

class NoInputBoolean extends Field
{
    public static string $view = 'moonshine::fields.no-input-boolean';

    protected bool $hideTrue = false;
    protected bool $hideFalse = false;

    public function hideTrue(mixed $condition = null): static
    {
        $this->hideTrue = Condition::boolean($condition, true);

        return $this;
    }

    public function hideFalse(mixed $condition = null): static
    {
        $this->hideFalse = Condition::boolean($condition, true);

        return $this;
    }

    public function formViewValue(Model $item): ?bool
    {
        return $this->getValue($item);
    }

    public function indexViewValue(Model $item, bool $container = true): View
    {
        return view('moonshine::fields.no-input-boolean', ['value' => $this->getValue($item)]);
    }

    public function exportViewValue(Model $item): mixed
    {
        return $this->getValue($item);
    }

    public function save(Model $item): Model
    {
        return $item;
    }

    protected function getValue(Model $item)
    {
        $value = $item->{$this->field()};

        if (is_callable($this->valueCallback())) {
            $value = $this->valueCallback()($item);
        }

        if (! $value && $this->hideFalse) {
            $value = null;
        } elseif ($value && $this->hideTrue) {
            $value = null;
        } else {
            $value = (bool)$value;
        }

        return $value;
    }
}
