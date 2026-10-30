<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

/**
 * Global display escaping defaults.
 * Kept separate from ConfiguratorContract so existing configurators stay compatible.
 */
interface DisplayEscapingConfiguratorContract
{
    public function isEscapeLabel(): bool;

    public function isEscapeHint(): bool;

    public function isEscapePrefix(): bool;

    public function isEscapeSuffix(): bool;

    public function isEscapeBeforeRender(): bool;

    public function isEscapeAfterRender(): bool;
}
