<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use MoonShine\Helpers\Condition;

class NoInput extends Field
{
    protected string $view = 'moonshine::fields.no-input';

    protected bool $isBoolean = false;

    protected bool $isLink = false;

    protected bool $hideTrue = false;

    protected bool $hideFalse = false;

    protected string|Closure $linkHref = '';

    public function boolean(
        mixed $hideTrue = null,
        mixed $hideFalse = null
    ): static {
        $this->hideTrue = Condition::boolean($hideTrue, false);
        $this->hideFalse = Condition::boolean($hideFalse, false);

        $this->isBoolean = true;

        return $this;
    }

    public function link(
        string|Closure $link = '#',
        bool $blank = false
    ): static {
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

        if ($this->isLink) {
            $href = $this->linkHref;

            if (is_closure($href)) {
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

        if ($this->isBoolean) {
            if ((! $value && $this->hideFalse) || ($value && $this->hideTrue)) {
                return '';
            }

            return (bool) $value;
        }

        return $value;
    }

    protected function resolveOnApply(): ?Closure
    {
        return static fn ($item) => $item;
    }
}
