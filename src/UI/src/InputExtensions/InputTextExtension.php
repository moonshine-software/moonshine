<?php

declare(strict_types=1);

namespace MoonShine\UI\InputExtensions;

use MoonShine\Support\DisplayHtml;
use MoonShine\UI\Enums\DisplayTextType;

/**
 * An input extension that displays text, such as a prefix or a suffix.
 */
abstract class InputTextExtension extends InputExtension
{
    protected ?bool $escape = null;

    abstract protected function getDisplayTextType(): DisplayTextType;

    public function escape(bool $escape = true): static
    {
        $this->escape = $escape;

        return $this;
    }

    public function isEscape(): bool
    {
        return $this->escape ?? $this->getDisplayTextType()->isEscapedBy($this->getCore()->getConfig());
    }

    protected function systemViewData(): array
    {
        return [
            ...parent::systemViewData(),
            'valueHtml' => DisplayHtml::make($this->getValue(), $this->isEscape()),
        ];
    }
}
