<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use MoonShine\Helpers\Condition;

class NoInput extends Field
{
    protected string $view = 'moonshine::fields.no-input';

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

    protected function resolvePreview(): string
    {
        $value = $this->toFormattedValue();

        if ($this->isRawMode()) {
            return $value;
        }

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
                $href = $href($value);
            }

            return view('moonshine::ui.url', [
                'value' => $value,
                'href' => $href,
                'blank' => $this->linkBlank,
            ])->render();
        }

        return (string) $value;
    }

    protected function resolveValue(): mixed
    {
        $value = $this->toFormattedValue();

        if ($this->isBadge && is_callable($this->badgeColorCallback)) {
            $this->badgeColor = call_user_func(
                $this->badgeColorCallback,
                $value
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
}
