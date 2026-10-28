<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use MoonShine\UI\Enums\DisplayTextType;

/**
 * Stores local escaping preferences per display text type.
 * Unset preferences fall back to the defaults resolved by DisplayTextType.
 */
trait WithDisplayEscaping
{
    /** @var array<string, bool> */
    protected array $displayEscaping = [];

    /**
     * Null removes the local preference, so the default applies again.
     */
    protected function setDisplayEscaping(DisplayTextType $type, ?bool $escape): static
    {
        if (\is_null($escape)) {
            unset($this->displayEscaping[$type->value]);

            return $this;
        }

        $this->displayEscaping[$type->value] = $escape;

        return $this;
    }

    protected function getDisplayEscaping(DisplayTextType $type): ?bool
    {
        return $this->displayEscaping[$type->value] ?? null;
    }

    protected function isDisplayEscaped(DisplayTextType $type): bool
    {
        return $this->getDisplayEscaping($type) ?? $type->isEscapedBy($this->getCore()->getConfig());
    }
}
