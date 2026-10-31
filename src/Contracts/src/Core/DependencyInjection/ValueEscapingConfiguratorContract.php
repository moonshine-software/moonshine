<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

/**
 * Global field value escaping defaults, independent of display escaping.
 * Kept separate from ConfiguratorContract so existing configurators stay compatible.
 */
interface ValueEscapingConfiguratorContract
{
    public function isEscapeValue(): bool;

    public function isEscapeValueOnApply(): bool;
}
