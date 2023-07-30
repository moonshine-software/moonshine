<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Helpers\Condition;

class NoInput extends Field
{
    protected static string $view = 'moonshine::fields.no-input';

    protected bool $isBadge = false;

    protected bool $isBoolean = false;

    protected bool $isLink = false;

    protected string $badgeColor = 'gray';

    protected ?Closure $badgeColorCallback = null;

    protected bool $hideTrue = false;

    protected bool $hideFalse = false;

    protected string|Closure $linkHref = '';


    public function badge(string|Closure|null $color = null): static
    {
        if (is_callable($color)) {
            $this->badgeColorCallback = $color;
        } elseif (! is_null($color)) {
            $this->badgeColor = $color;
        }

        $this->isBadge = true;
        $this->isBoolean = false;
        $this->isLink = false;

        return $this;
    }

    public function boolean(
        mixed $hideTrue = null,
        mixed $hideFalse = null
    ): static {
        $this->hideTrue = Condition::boolean($hideTrue, false);
        $this->hideFalse = Condition::boolean($hideFalse, false);

        $this->isBadge = false;
        $this->isBoolean = true;
        $this->isLink = false;

        return $this;
    }

    public function link(
        string|Closure $link = '#',
        bool $blank = false
    ): static {
        $this->isBadge = false;
        $this->isBoolean = false;
        $this->isLink = true;

        $this->linkHref = $link;
        $this->linkBlank = $blank;

        return $this;
    }

    public function save(Model $item): Model
    {
        return $item;
    }

    public function formViewValue(Model $item): string
    {
        return $this->indexViewValue($item);
    }

    public function indexViewValue(Model $item, bool $container = true): string
    {
        $value = $this->getValue($item, $container);

        if ($this->isBoolean) {
            return view('moonshine::ui.boolean', [
                'value' => $value,
            ])->render();
        }

        if ($this->isBadge) {
            return view('moonshine::ui.badge', [
                'color' => $this->badgeColor,
                'value' => $value,
            ])->render();
        }

        if ($this->isLink) {
            $href = $this->linkHref;

            if (is_callable($href)) {
                $href = $href($item);
            }

            return view('moonshine::ui.url', [
                'value' => $value,
                'href' => $href,
                'blank' => $this->linkBlank,
            ])->render();
        }

        return (string) $value;
    }

    public function getValue(Model $item = null, bool $container = true): string|bool
    {
        $value = parent::indexViewValue($item, $container);

        if ($this->isBadge && is_callable($this->badgeColorCallback)) {
            $this->badgeColor = call_user_func(
                $this->badgeColorCallback,
                $item
            );
        }

        if ($this->isBoolean) {
            if ((! $value && $this->hideFalse) || ($value && $this->hideTrue)) {
                return '';
            }

            return (bool) $value;
        }

        return $value;
    }

    public function exportViewValue(Model $item): string
    {
        return (string) $this->getValue($item);
    }
}
