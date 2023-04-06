<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Helpers\Condition;

class NoInput extends Field
{
    public static string $view = 'moonshine::fields.no-input';

    protected bool $isBadge = false;

    protected bool $isBoolean = false;

    protected string $badgeColor = 'gray';

    protected ?Closure $badgeColorCallback = null;

    protected bool $hideTrue = false;

    protected bool $hideFalse = false;

    public function badge(string|Closure|null $color = null): static
    {
        if (is_callable($color)) {
            $this->badgeColorCallback = $color;
        } elseif (!is_null($color)) {
            $this->badgeColor = $color;
        }

        $this->isBadge = true;
        $this->isBoolean = false;

        return $this;
    }

    public function boolean(mixed $hideTrue = null, mixed $hideFalse = null): static
    {
        $this->hideTrue = Condition::boolean($hideTrue, false);
        $this->hideFalse = Condition::boolean($hideFalse, false);

        $this->isBadge = false;
        $this->isBoolean = true;

        return $this;
    }

    public function save(Model $item): Model
    {
        return $item;
    }

    public function indexViewValue(Model $item, bool $container = true): string|bool|null
    {
        $value = $this->getValue($item, $container);

        if ($this->isBoolean) {
            return view('moonshine::ui.boolean', [
                'value' => $value
            ])->render();
        }

        if ($this->isBadge) {
            return view('moonshine::ui.badge', [
                'color' => $this->badgeColor,
                'value' => $value
            ])->render();
        }

        return $value;
    }

    public function formViewValue(Model $item): string|bool|null
    {
        return $this->getValue($item);
    }

    public function exportViewValue(Model $item): string|bool|null
    {
        return $this->getValue($item);
    }

    public function getValue(Model $item, bool $container = true): null|bool|string
    {
        $value = parent::indexViewValue($item, $container);

        if ($this->isBadge && is_callable($this->badgeColorCallback)) {
            $this->badgeColor = call_user_func($this->badgeColorCallback, $item);
        }

        if ($this->isBoolean) {
            if ((!$value && $this->hideFalse) || ($value && $this->hideTrue)) {
                return null;
            }

            return (bool)$value;
        }

        return (string)$value;
    }
}
