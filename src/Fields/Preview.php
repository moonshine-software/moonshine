<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Support\Condition;

class Preview extends Field
{
    protected string $view = 'moonshine::fields.preview';

    protected bool $canBeEmpty = true;

    protected bool $isBoolean = false;

    protected bool $hideTrue = false;

    protected bool $hideFalse = false;

    public function boolean(
        mixed $hideTrue = null,
        mixed $hideFalse = null
    ): static {
        $this->hideTrue = Condition::boolean($hideTrue, false);
        $this->hideFalse = Condition::boolean($hideFalse, false);

        $this->isBoolean = true;

        return $this;
    }

    protected function resolvePreview(): View|string
    {
        $value = $this->toFormattedValue();

        if ($this->isRawMode()) {
            return $value;
        }

        if ($this->isBoolean) {
            return view('moonshine::ui.boolean', [
                'value' => $value,
            ]);
        }

        return (string) $value;
    }

    protected function resolveValue(): mixed
    {
        return $this->preview();
    }

    protected function resolveOnApply(): ?Closure
    {
        return static fn ($item) => $item;
    }
}
